<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The production `personal_access_tokens` table predates Sanctum 2.x
 * (missing `tokenable_id` / `tokenable_type` columns), so token issue
 * fails with "Column not found". Recreate the table with the schema
 * Sanctum 2.11 expects, including `expires_at` used by our 6-month
 * config.expiration.
 *
 * Drop is safe: there are no usable existing tokens (the schema is
 * broken — Sanctum couldn't write them).
 */
class FixPersonalAccessTokensSchema extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tokenable_type');
            $table->unsignedBigInteger('tokenable_id');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    public function down(): void
    {
        // No-op: this is a corrective migration; we don't reverse it.
    }
}
