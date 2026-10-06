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
        Schema::create('search_indexes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('note_id')
                ->constrained('notes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->ulidMorphs('searchable');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_indexes');
    }
};
