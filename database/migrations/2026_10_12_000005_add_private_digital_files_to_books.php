<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('books', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->string('preview_path')->nullable();
            $table->unsignedSmallInteger('preview_pages')->default(10);
            $table->unsignedBigInteger('file_size')->nullable();
        });
    }
    public function down(): void {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'preview_path', 'preview_pages', 'file_size']);
        });
    }
};
