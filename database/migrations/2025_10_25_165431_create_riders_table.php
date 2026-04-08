<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('name');
            $table->string('phone');
            $table->string('license');
            $table->string('vehicle_number');
            $table->string('vehicle');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->string('status')->default('pending');
            // $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riders');
    }




    
};
