<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nextday_namber;
use Illuminate\Support\Facades\Log;

class ClearNextdayNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nextday:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nextday_namber jadvalini har kuni tozalash';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Nextday_namber jadvalini tozalash boshlandi...');
            
            // Jadvaldagi ma'lumotlar sonini olish
            $count = Nextday_namber::count();
            
            if ($count > 0) {
                // Barcha ma'lumotlarni o'chirish
                Nextday_namber::truncate();
                
                $this->info("Muvaffaqiyatli tozalandi. {$count} ta yozuv o'chirildi.");
                
                // Log yozish
                Log::info("Nextday_namber jadvali tozalandi. {$count} ta yozuv o'chirildi.", [
                    'command' => 'nextday:clear',
                    'timestamp' => now(),
                    'records_deleted' => $count
                ]);
            } else {
                $this->info('Jadvalda o\'chiriladigan ma\'lumot yo\'q.');
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Xatolik yuz berdi: ' . $e->getMessage());
            
            // Log yozish
            Log::error('Nextday_namber jadvalini tozalashda xatolik', [
                'command' => 'nextday:clear',
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            
            return Command::FAILURE;
        }
    }
}

