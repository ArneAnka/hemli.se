<?php

// database/migrations/xxxx_xx_xx_add_used_at_to_guest_uploads.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('guest_uploads', function (Blueprint $table) {
            $table->timestamp('used_at')->nullable()->after('expires_at');
        });
    }
    public function down(): void {
        Schema::table('guest_uploads', function (Blueprint $table) {
            $table->dropColumn('used_at');
        });
    }
};
