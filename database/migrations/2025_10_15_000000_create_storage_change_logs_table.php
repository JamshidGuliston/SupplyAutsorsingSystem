<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorageChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_change_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kingarden_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('day_id')->nullable();
            $table->string('type'); // 'plus', 'minus', 'residual'
            $table->decimal('old_value', 10, 2)->default(0);
            $table->decimal('new_value', 10, 2)->default(0);
            $table->decimal('difference', 10, 2)->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->timestamps();
            
            $table->index('kingarden_id');
            $table->index('product_id');
            $table->index('day_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_change_logs');
    }
}

