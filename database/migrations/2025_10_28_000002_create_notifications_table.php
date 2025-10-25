<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // notification turi (children_count_changed, etc.)
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->text('data'); // JSON ma'lumotlar
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes - qisqa nomlar bilan
            $table->index(['notifiable_type', 'notifiable_id'], 'notifications_notifiable_idx');
            $table->index(['type', 'read_at'], 'notifications_type_read_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
