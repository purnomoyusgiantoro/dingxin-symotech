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
        // 1. Drivers table
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('driver_code')->unique();
            $table->string('area_code');
            $table->string('phone_number')->nullable();
            $table->string('plate_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('cumulative_balance', 15, 2)->default(0);
            $table->timestamps();
        });

        // 2. Daily Deliveries table
        Schema::create('daily_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('route_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['driver_id', 'date']);
        });

        // 3. Return Items table
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Transfer Payments table
        Schema::create('transfer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('date');
            $table->string('store_name');
            $table->decimal('claimed_amount', 15, 2);
            $table->decimal('verified_amount', 15, 2)->nullable();
            $table->string('proof_image_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 5. Credit Deliveries table
        Schema::create('credit_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('date');
            $table->string('store_name');
            $table->decimal('amount', 15, 2);
            $table->string('invoice_photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Cash Deposits table
        Schema::create('cash_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount_received', 15, 2);
            $table->string('deposit_phase')->default('1');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Daily Settlements table
        Schema::create('daily_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount_carried', 15, 2)->default(0);
            $table->decimal('amount_returned', 15, 2)->default(0);
            $table->decimal('amount_transfer_approved', 15, 2)->default(0);
            $table->decimal('amount_credit', 15, 2)->default(0);
            $table->decimal('target_cash', 15, 2)->default(0);
            $table->decimal('actual_cash', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->enum('status', ['lunas', 'kurang_setor', 'lebih_setor'])->default('lunas');
            $table->timestamps();

            $table->unique(['driver_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_settlements');
        Schema::dropIfExists('cash_deposits');
        Schema::dropIfExists('credit_deliveries');
        Schema::dropIfExists('transfer_payments');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('daily_deliveries');
        Schema::dropIfExists('drivers');
    }
};
