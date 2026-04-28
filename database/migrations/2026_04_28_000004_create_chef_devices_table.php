<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChefDevicesTable extends Migration
{
    public function up(): void
    {
        Schema::create('chef_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('platform', ['android', 'ios']);
            $table->string('fcm_token');
            $table->string('device_model', 100)->nullable();
            $table->string('app_version', 20)->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'fcm_token'], 'uniq_user_token');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_devices');
    }
}
