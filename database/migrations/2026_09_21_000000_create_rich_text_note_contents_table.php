<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rich_text_note_contents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('note_id')
                ->constrained('notes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->jsonb('content');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rich_text_note_contents');
    }
};
