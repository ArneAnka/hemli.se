<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('guest_uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('original_name');
            $table->string('mime', 191)->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->string('storage_path'); // storage/app/guest/...
            $table->timestamp('expires_at');
            $table->string('download_token', 64)->unique();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('guest_uploads');
    }
};
