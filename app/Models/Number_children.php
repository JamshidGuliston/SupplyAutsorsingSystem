<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Number_children extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
		'id',
		'kingar_name_id',
		'day_id',
		'king_age_name_id',
		'kingar_children_number',
		'workers_count',
		'kingar_menu_id',
	];
	
	// O'tgan kunlar uchun ma'lumot qo'shish
	public static function addPastDaysData($daysBack = 30) {
		$startDate = Carbon::now()->subDays($daysBack);
		$endDate = Carbon::now()->subDay(); // Kechagi kungacha
		
		// Barcha bog'chalar
		$kindergartens = \App\Models\Kindgarden::where('hide', 1)->get();
		
		// Barcha yosh guruhlari
		$ageRanges = \App\Models\Age_range::all();
		
		// Barcha kunlar
		$days = \App\Models\Day::whereBetween('created_at', [$startDate, $endDate])->get();
		
		$addedCount = 0;
		
		foreach ($days as $day) {
			foreach ($kindergartens as $kindergarten) {
				foreach ($ageRanges as $ageRange) {
					// Mavjud ma'lumotni tekshirish
					$existing = self::where('day_id', $day->id)
						->where('kingar_name_id', $kindergarten->id)
						->where('king_age_name_id', $ageRange->id)
						->first();
					
					if (!$existing) {
						// Yangi ma'lumot qo'shish
						self::create([
							'kingar_name_id' => $kindergarten->id,
							'day_id' => $day->id,
							'king_age_name_id' => $ageRange->id,
							'kingar_children_number' => 10, // Boshlang'ich qiymat
							'workers_count' => 10, // Boshlang'ich qiymat
							'kingar_menu_id' => null, // Boshlang'ich qiymat
						]);
						$addedCount++;
					}
				}
			}
		}
		
		return $addedCount;
	}
	
	// Bog'cha bilan bog'lanish
	public function kindergarten() {
		return $this->belongsTo(\App\Models\Kindgarden::class, 'kingar_name_id');
	}
	
	// Yosh guruhi bilan bog'lanish
	public function ageRange() {
		return $this->belongsTo(\App\Models\Age_range::class, 'king_age_name_id');
	}
	
	// Kun bilan bog'lanish
	public function day() {
		return $this->belongsTo(\App\Models\Day::class, 'day_id');
	}
}
