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
        Schema::create('channels', function (Blueprint $table) {
           $table->id();
            // Link channel to department (cascade delete if department is deleted)
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name'); // e.g., "general"
            $table->string('slug'); // e.g., "general"
            $table->enum('type', ['public', 'private', 'direct'])->default('public');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['department_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
