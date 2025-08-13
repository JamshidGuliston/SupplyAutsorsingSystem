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
        'payment_type',
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
                if ($payment->payment_type == 'storage') {
                    // Storage to'lovi uchun - loan qarzlarni orqaga qaytarish
                    $existingDebts = debts::where('shop_id', $payment->shop_id)
                        ->where('loan', '>=', 0)
                        ->where('debt_type', 'storage')
                        ->orderBy('day_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->get();
                    
                    $remainingAmount = $payment->paid_to_debts;
                    
                    foreach ($existingDebts as $debt) {
                        if ($remainingAmount > 0) {
                            $currentPay = $debt->pay;
                            
                            if ($currentPay > 0) {
                                if ($remainingAmount >= $currentPay) {
                                    $debt->update([
                                        'pay' => 0,
                                        'loan' => $debt->loan + $currentPay
                                    ]);
                                    $remainingAmount -= $currentPay;
                                } else {
                                    $debt->update([
                                        'pay' => $currentPay - $remainingAmount,
                                        'loan' => $debt->loan + $remainingAmount
                                    ]);
                                    $remainingAmount = 0;
                                }
                            }
                        }
                    }
                } elseif ($payment->payment_type == 'sale') {
                    // Sale to'lovi uchun - hisloan qarzlarni orqaga qaytarish
                    $existingDebts = debts::where('shop_id', $payment->shop_id)
                        ->where('hisloan', '>=', 0)
                        ->where('debt_type', 'sale')
                        ->orderBy('day_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->get();
                    
                    $remainingAmount = $payment->paid_to_debts;
                    
                    foreach ($existingDebts as $debt) {
                        if ($remainingAmount > 0) {
                            $currentPay = $debt->pay;
                            
                            if ($currentPay > 0) {
                                if ($remainingAmount >= $currentPay) {
                                    $debt->update([
                                        'pay' => 0,
                                        'hisloan' => $debt->hisloan + $currentPay
                                    ]);
                                    
                                    // Sale ni ham yangilash
                                    $sale = Sale::find($debt->sale_id);
                                    if ($sale) {
                                        $sale->update([
                                            'paid_amount' => $sale->paid_amount - $currentPay,
                                            'status' => $sale->paid_amount - $currentPay > 0 ? 'partial' : 'pending'
                                        ]);
                                    }
                                    
                                    $remainingAmount -= $currentPay;
                                } else {
                                    $debt->update([
                                        'pay' => $currentPay - $remainingAmount,
                                        'hisloan' => $debt->hisloan + $remainingAmount
                                    ]);
                                    
                                    // Sale ni ham yangilash
                                    $sale = Sale::find($debt->sale_id);
                                    if ($sale) {
                                        $sale->update([
                                            'paid_amount' => $sale->paid_amount - $remainingAmount,
                                            'status' => $sale->paid_amount - $remainingAmount > 0 ? 'partial' : 'pending'
                                        ]);
                                    }
                                    
                                    $remainingAmount = 0;
                                }
                            }
                        }
                    }
                }
            }
            
            // Agar ortiqcha pul bo'lsa, uni ham orqaga qaytarish
            if ($payment->excess_amount > 0) {
                debts::where('shop_id', $payment->shop_id)
                    ->where('day_id', $payment->day_id)
                    ->where('hisloan', $payment->excess_amount)
                    ->where('loan', 0)
                    ->where('pay', $payment->excess_amount)
                    ->where('debt_type', $payment->payment_type)
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