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
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('listings', 'server')) {
                $table->dropColumn('server');
            }
            if (Schema::hasColumn('listings', 'account_age')) {
                $table->dropColumn('account_age');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'description')) {
                $table->text('description');
            }
            if (!Schema::hasColumn('listings', 'server')) {
                $table->string('server')->nullable();
            }
            if (!Schema::hasColumn('listings', 'account_age')) {
                $table->string('account_age')->nullable();
            }
        });
    }
};
