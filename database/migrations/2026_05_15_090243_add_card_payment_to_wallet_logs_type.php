<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE wallet_logs DROP CONSTRAINT wallet_logs_type_check");

        DB::statement("ALTER TABLE wallet_logs ADD CONSTRAINT wallet_logs_type_check 
            CHECK (type IN ('topup', 'purchase', 'payout', 'refund', 'withdrawal', 'card_payment'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE wallet_logs DROP CONSTRAINT wallet_logs_type_check");

        DB::statement("ALTER TABLE wallet_logs ADD CONSTRAINT wallet_logs_type_check 
            CHECK (type IN ('topup', 'purchase', 'payout', 'refund', 'withdrawal'))");
    }
};