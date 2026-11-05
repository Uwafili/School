<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('store_approval_status')->default('approved')->after('status');
            $table->timestamp('store_approved_at')->nullable()->after('store_approval_status');
            $table->timestamp('store_rejected_at')->nullable()->after('store_approved_at');
            $table->text('store_rejection_reason')->nullable()->after('store_rejected_at');
            $table->json('items')->nullable()->after('items_description');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('refund_status')->default('none')->after('payment_reference');
            $table->string('refund_reference')->nullable()->after('refund_status');
            $table->text('refund_message')->nullable()->after('refund_reference');
            $table->timestamp('refund_attempted_at')->nullable()->after('refund_message');
            $table->index(['store_id', 'store_approval_status']);
            $table->unique(['payment_reference', 'store_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('message');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type')->default('order_assigned')->change();
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        DB::table('notifications')
            ->whereNotIn('type', ['order_assigned', 'order_completed', 'order_cancelled'])
            ->update(['type' => 'order_assigned']);

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
            $table->enum('type', ['order_assigned', 'order_completed', 'order_cancelled'])
                ->default('order_assigned')
                ->change();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'store_approval_status']);
            $table->dropUnique(['payment_reference', 'store_id']);
            $table->dropColumn([
                'store_approval_status',
                'store_approved_at',
                'store_rejected_at',
                'store_rejection_reason',
                'items',
                'payment_reference',
                'refund_status',
                'refund_reference',
                'refund_message',
                'refund_attempted_at',
            ]);
        });
    }
};
