<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChefLocationEventsTable extends Migration
{
    public function up(): void
    {
        Schema::create('chef_location_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kindgarden_id');
            $table->enum('event_type', ['exit', 'enter', 'beacon']);
            $table->dateTime('happened_at');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->unsignedInteger('distance_m');
            $table->boolean('is_mock')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'happened_at'], 'idx_user_happened');
            $table->index(['kindgarden_id', 'happened_at'], 'idx_kindgarden_happened');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('kindgarden_id')->references('id')->on('kindgardens');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_location_events');
    }
}
