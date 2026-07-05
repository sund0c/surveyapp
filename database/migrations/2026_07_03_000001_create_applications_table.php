<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // e.g. "Perisai"
            $table->string('slug')->unique();             // e.g. "perisai"
            $table->string('api_key', 64)->unique();      // public identifier, sent in header
            $table->text('api_secret');                   // ENCRYPTED (see Application::casts), never bcrypt-hashed
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['api_key', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
