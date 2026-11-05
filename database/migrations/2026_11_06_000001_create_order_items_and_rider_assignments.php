<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->nullable()->constrained('posts')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
            $table->index(['order_id', 'post_id']);
        });

        Schema::create('rider_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status');
            $table->timestamp('assigned_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'rider_id', 'released_at']);
        });

        DB::table('orders')->select(['id', 'items', 'rider_id', 'status', 'created_at', 'updated_at', 'rider_accepted_at', 'rider_arrived_at', 'picked_up_at'])->orderBy('id')->chunkById(250, function ($orders) {
            foreach ($orders as $order) {
                foreach (json_decode($order->items ?? '[]', true) ?: [] as $item) {
                    if (! is_array($item)
                        || ! isset($item['title'], $item['unit_price'], $item['quantity'], $item['line_total'])) {
                        continue;
                    }

                    $postId = isset($item['post_id'])
                        && DB::table('posts')->where('id', $item['post_id'])->exists()
                        ? $item['post_id']
                        : null;

                    DB::table('order_items')->insert([
                        'order_id' => $order->id,
                        'post_id' => $postId,
                        'product_name' => $item['title'],
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'line_total' => $item['line_total'],
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ]);
                }

                if (! $order->rider_id) {
                    continue;
                }

                $completed = $order->status === 'delivered';
                $released = in_array($order->status, ['cancelled', 'rejected'], true);
                DB::table('rider_assignments')->insert([
                    'order_id' => $order->id,
                    'rider_id' => $order->rider_id,
                    'status' => $completed ? 'completed' : ($released ? 'released' : ($order->rider_accepted_at ? 'accepted' : 'assigned')),
                    'assigned_at' => $order->created_at,
                    'acknowledged_at' => $order->rider_accepted_at,
                    'arrived_at' => $order->rider_arrived_at,
                    'picked_up_at' => $order->picked_up_at,
                    'completed_at' => $completed ? $order->updated_at : null,
                    'released_at' => $released ? $order->updated_at : null,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_assignments');
        Schema::dropIfExists('order_items');
    }
};
