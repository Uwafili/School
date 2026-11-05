<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_reference')->nullable()->after('id');
        });

        DB::table('orders')
            ->whereNull('order_reference')
            ->orderBy('id')
            ->chunkById(500, function ($orders) {
                foreach ($orders as $order) {
                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update(['order_reference' => 'FS-' . Str::upper((string) Str::uuid())]);
                }
            });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('order_reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['order_reference']);
            $table->dropColumn('order_reference');
        });
    }
};
