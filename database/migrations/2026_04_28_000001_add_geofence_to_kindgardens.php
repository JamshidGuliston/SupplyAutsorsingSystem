<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeofenceToKindgardens extends Migration
{
    public function up(): void
    {
        Schema::table('kindgardens', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('id');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->unsignedInteger('geofence_radius')->default(200)->after('lng');
        });
    }

    public function down(): void
    {
        Schema::table('kindgardens', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng', 'geofence_radius']);
        });
    }
}
