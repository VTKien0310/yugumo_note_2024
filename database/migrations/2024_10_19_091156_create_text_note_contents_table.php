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
        Schema::create('text_note_contents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('note_id')
                ->constrained('notes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->longText('content');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_note_contents');
    }
};
