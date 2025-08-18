<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        // order_products jadvaliga data_of_weight ustunini qo'shish
        Schema::table('order_products', function (Blueprint $table) {
            $table->json('data_of_weight')->nullable()->after('document_processes_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // order_products jadvalidan data_of_weight ustunini olib tashlash
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('data_of_weight');
        });        
    }
}; 