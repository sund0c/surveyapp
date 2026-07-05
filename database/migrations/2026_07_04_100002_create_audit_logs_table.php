<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Deliberately NOT a foreign key with cascadeOnDelete - if a user
            // account is later deleted, the audit trail must survive intact.
            // actor_name/actor_email are a snapshot at the time of the action.
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_name')->nullable();
            $table->string('actor_email')->nullable();

            $table->string('action', 100);          // e.g. 'user.created', 'auth.login_failed'
            $table->text('description');

            $table->string('subject_type', 100)->nullable(); // e.g. 'User', 'Application'
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
