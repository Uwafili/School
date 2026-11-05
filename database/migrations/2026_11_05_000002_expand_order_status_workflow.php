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
            $table->string('status')->default('pending')->change();
        });

        DB::table('orders')->where('status', 'assigned')->update(['status' => 'rider_assigned']);
        DB::table('orders')->where('status', 'completed')->update(['status' => 'delivered']);
        DB::table('orders')
            ->where('status', 'accepted')
            ->whereNull('picked_up_at')
            ->update(['status' => 'rider_assigned']);
        DB::table('orders')
            ->where('status', 'accepted')
            ->whereNotNull('picked_up_at')
            ->update(['status' => 'out_for_delivery']);
        DB::table('orders')
            ->where('status', 'pending')
            ->where('store_approval_status', 'approved')
            ->whereNull('rider_id')
            ->update(['status' => 'approved']);
    }

    public function down(): void
    {
        DB::table('orders')->whereIn('status', ['approved', 'preparing', 'ready_for_pickup'])->update(['status' => 'pending']);
        DB::table('orders')->where('status', 'rider_assigned')->update(['status' => 'assigned']);
        DB::table('orders')->whereIn('status', ['picked_up', 'out_for_delivery'])->update(['status' => 'accepted']);
        DB::table('orders')->where('status', 'delivered')->update(['status' => 'completed']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'assigned', 'accepted', 'rejected', 'completed', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }
};
