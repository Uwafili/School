<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_online');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('rider_accepted_at')->nullable()->after('picked_up_at');
            $table->timestamp('rider_arrived_at')->nullable()->after('rider_accepted_at');
            $table->timestamp('estimated_delivery_at')->nullable()->after('rider_arrived_at');
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->timestamps();
            $table->index(['order_id', 'created_at']);
        });

        Schema::create('delivery_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->timestamp('attempted_at');
            $table->timestamps();
            $table->index(['order_id', 'attempted_at']);
        });

        DB::table('orders')->orderBy('id')->chunkById(500, function ($orders) {
            foreach ($orders as $order) {
                DB::table('order_status_histories')->insert([
                    'order_id' => $order->id,
                    'status' => 'pending',
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);

                if ($order->status !== 'pending') {
                    DB::table('order_status_histories')->insert([
                        'order_id' => $order->id,
                        'status' => $order->status,
                        'created_at' => $order->updated_at,
                        'updated_at' => $order->updated_at,
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_attempts');
        Schema::dropIfExists('order_status_histories');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['rider_accepted_at', 'rider_arrived_at', 'estimated_delivery_at']);
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
