<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckOutUndoCountToChefAttendances extends Migration
{
    public function up(): void
    {
        Schema::table('chef_attendances', function (Blueprint $table) {
            $table->unsignedInteger('check_out_undo_count')
                ->default(0)
                ->after('check_out_replaced_count');
        });
    }

    public function down(): void
    {
        Schema::table('chef_attendances', function (Blueprint $table) {
            $table->dropColumn('check_out_undo_count');
        });
    }
}
