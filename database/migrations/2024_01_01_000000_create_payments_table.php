<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id');
            $table->integer('day_id');
            $table->integer('total_amount'); // To'lanayotgan pul umumiy cash, card, transfer
            $table->integer('cash_amount'); // Naqt pul
            $table->integer('card_amount')->default(0); // Karta pul
            $table->integer('transfer_amount')->default(0); // Pul o'tkazish
            $table->integer('paid_to_debts'); // Qarzlarni yopish uchun sarflangan pul
            $table->string('image')->nullable(); // Rasm
            $table->integer('excess_amount')->default(0); // Ortiqcha pul
            $table->text('description')->nullable(); // Izoh
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
} 