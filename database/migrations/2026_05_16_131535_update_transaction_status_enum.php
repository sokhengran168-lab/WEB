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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('bank_name')->nullable()->after('payment_reference');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->timestamp('buyer_paid_at')->nullable()->after('bank_account_name');
            $table->timestamp('admin_confirmed_at')->nullable()->after('buyer_paid_at');
            $table->text('payment_note')->nullable()->after('admin_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_reference',
                'bank_name',
                'bank_account_name',
                'buyer_paid_at',
                'admin_confirmed_at',
                'payment_note',
            ]);
        });
    }
};
