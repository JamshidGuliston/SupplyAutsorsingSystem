<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaleIdToDebtsTable extends Migration
{
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->integer('sale_id')->nullable()->after('shop_id');
            $table->string('debt_type')->default('storage'); // 'storage' yoki 'sale'
        });
    }

    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn(['sale_id', 'debt_type']);
        });
    }
} 