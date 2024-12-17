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
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->boolean('draft');
            // $table->enum('status', ['Published', 'Draft'])->default('Published');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->string('cover');
            $table->longText('desc');
            $table->longText('body');
            $table->integer('favorite')->default(0);
            $table->string('added_by');
            $table->string('last_edited_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
