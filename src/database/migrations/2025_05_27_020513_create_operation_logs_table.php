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
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('operation_type'); // reverse, copy, duplicate, replace
            $table->string('input_filename');
            $table->string('output_filename')->nullable();
            $table->text('operation_details')->nullable(); // JSON形式で詳細情報
            $table->decimal('execution_time', 8, 4)->nullable(); // 実行時間（秒）
            $table->enum('status', ['success', 'error'])->default('success');
            $table->text('error_message')->nullable();
            $table->string('file_path')->nullable(); // 保存されたファイルのパス
            $table->integer('file_size')->nullable(); // ファイルサイズ（バイト）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_logs');
    }
};
