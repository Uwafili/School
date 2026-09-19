<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('recipient_code', 6)->nullable()->after('notes');
            $table->timestamp('recipient_verified_at')->nullable()->after('recipient_code');
        });
    }

    public function down(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('is_online');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['recipient_code', 'recipient_verified_at']);
        });
    }
};
