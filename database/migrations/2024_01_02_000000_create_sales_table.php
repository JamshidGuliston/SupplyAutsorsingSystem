<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('buyer_shop_id'); // Xaridor shop ID
            $table->integer('user_id')->nullable();
            $table->integer('day_id');
            $table->string('invoice_number')->unique()->nullable(); // Faktura raqami
            $table->integer('total_amount')->nullable(); // Jami summa
            $table->integer('paid_amount')->nullable(); // To'langan summa
            $table->integer('debt_amount')->nullable(); // Qarz summa
            $table->string('image')->nullable();
            $table->enum('status', ['pending', 'paid', 'partial'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
} 