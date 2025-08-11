<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'shop_id',
        'day_id',
        'total_amount',
        'cash_amount',
        'card_amount',
        'transfer_amount',
        'paid_to_debts',
        'excess_amount',
        'image',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        // Payment o'chirilganda debts jadvalidagi o'zgarishlarni orqaga qaytarish
        static::deleting(function ($payment) {
            // Agar payment qarzlarni yopish uchun ishlatilgan bo'lsa
            if ($payment->paid_to_debts > 0) {
                // Firma bo'yicha mavjud qarzlarni topish
                $existingDebts = debts::where('shop_id', $payment->shop_id)
                    ->where('loan', '>=', 0)
                    ->orderBy('day_id', 'desc') // Eng yangi qarzlardan boshlab
                    ->orderBy('id', 'desc')
                    ->get();
                
                $remainingAmount = $payment->paid_to_debts; // Qaytarilishi kerak bo'lgan pul
                
                // Qarzlarni orqaga qaytarish
                foreach ($existingDebts as $debt) {
                    if ($remainingAmount > 0) {
                        $currentPay = $debt->pay; // Hozirgi to'langan miqdor
                        
                        if ($currentPay > 0) {
                            if ($remainingAmount >= $currentPay) {
                                // To'liq to'lovni orqaga qaytarish
                                $debt->update([
                                    'pay' => 0,
                                    'loan' => $debt->loan + $currentPay
                                ]);
                                $remainingAmount -= $currentPay;
                            } else {
                                // Qisman to'lovni orqaga qaytarish
                                $debt->update([
                                    'pay' => $currentPay - $remainingAmount,
                                    'loan' => $debt->loan + $remainingAmount
                                ]);
                                $remainingAmount = 0;
                            }
                        }
                    }
                }
            }
            
            // Agar ortiqcha pul bo'lsa, uni ham orqaga qaytarish
            if ($payment->excess_amount > 0) {
                // Ortiqcha pul uchun yaratilgan debts yozuvini topish va o'chirish
                debts::where('shop_id', $payment->shop_id)
                    ->where('day_id', $payment->day_id)
                    ->where('hisloan', $payment->excess_amount)
                    ->where('loan', 0)
                    ->where('pay', $payment->excess_amount)
                    ->delete();
            }
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }
} 