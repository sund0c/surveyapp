<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('rating'); // 1-5, enforced in FormRequest + DB check below
            $table->text('comment')->nullable();

            // Privacy-conscious metadata: hash the origin IP rather than storing raw,
            // still useful to correlate anomalous submission bursts in Wazuh/dashboard.
            $table->string('origin_ip_hash', 64)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['application_id', 'created_at']);
        });

        // Defense-in-depth: DB-level CHECK constraint (MySQL 8.0.16+ / MariaDB 10.2+ enforce this).
        // App-layer validation in StoreRatingRequest is the primary control either way.
        try {
            \Illuminate\Support\Facades\DB::statement(
                'ALTER TABLE ratings ADD CONSTRAINT ratings_rating_range CHECK (rating BETWEEN 1 AND 5)'
            );
        } catch (\Throwable $e) {
            // Older MySQL without CHECK enforcement - safe to ignore, app-layer still validates
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
