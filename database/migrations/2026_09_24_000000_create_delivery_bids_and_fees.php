<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('total_price');
            $table->index(['store_id', 'status']);
            $table->index(['customer_id', 'status']);
        });

        Schema::create('delivery_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('rider_id')->constrained('riders')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->unique(['order_id', 'rider_id']);
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_bids');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'status']);
            $table->dropIndex(['customer_id', 'status']);
            $table->dropColumn('delivery_fee');
        });
    }
};
