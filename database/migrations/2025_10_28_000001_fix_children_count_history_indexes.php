<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixChildrenCountHistoryIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Agar jadval mavjud bo'lsa, uni o'chirib qayta yaratamiz
        if (Schema::hasTable('children_count_history')) {
            Schema::dropIfExists('children_count_history');
        }
        
        Schema::create('children_count_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kingar_name_id')->constrained('kindgardens')->onDelete('cascade');
            $table->unsignedBigInteger('king_age_name_id')->constrained('age_ranges')->onDelete('cascade');
            $table->integer('old_children_count')->nullable();
            $table->integer('new_children_count');
            $table->unsignedBigInteger('changed_by')->constrained('users')->onDelete('cascade'); // Kim o'zgartirgan
            $table->timestamp('changed_at');
            $table->text('change_reason')->nullable(); // O'zgartirish sababi
            $table->timestamps();

            // Indexes - qisqa nomlar bilan
            $table->index(['kingar_name_id', 'changed_at'], 'cc_hist_kingar_changed');
            $table->index(['kingar_name_id', 'king_age_name_id', 'changed_at'], 'cc_hist_kingar_age_changed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('children_count_history');
    }
}
