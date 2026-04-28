<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChefAttendancesTable extends Migration
{
    public function up(): void
    {
        Schema::create('chef_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kindgarden_id');
            $table->date('date');

            $table->dateTime('check_in_at')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_lng', 10, 7)->nullable();
            $table->unsignedInteger('check_in_distance_m')->nullable();
            $table->string('check_in_selfie_path')->nullable();
            $table->boolean('check_in_is_late')->default(false);
            $table->unsignedInteger('check_in_replaced_count')->default(0);

            $table->dateTime('check_out_at')->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_lng', 10, 7)->nullable();
            $table->unsignedInteger('check_out_distance_m')->nullable();
            $table->string('check_out_selfie_path')->nullable();
            $table->unsignedInteger('check_out_replaced_count')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'date'], 'uniq_user_date');
            $table->index(['kindgarden_id', 'date'], 'idx_kindgarden_date');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('kindgarden_id')->references('id')->on('kindgardens');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_attendances');
    }
}
