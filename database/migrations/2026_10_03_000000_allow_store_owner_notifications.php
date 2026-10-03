<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('notifications')->whereNull('rider_id')->delete();

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->foreignId('rider_id')->nullable(false)->change();
        });
    }
};