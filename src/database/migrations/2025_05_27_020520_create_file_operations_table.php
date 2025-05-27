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
        Schema::create('file_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_log_id')->constrained()->onDelete('cascade');
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->string('operation_directory'); // file-reverse, file-copy, etc.
            $table->integer('file_size');
            $table->string('mime_type')->nullable();
            $table->text('file_content_preview')->nullable(); // 最初の数行のプレビュー
            $table->boolean('is_downloaded')->default(false);
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_operations');
    }
};
