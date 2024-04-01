<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_account_id');
            $table->unsignedBigInteger('recipient_account_id');

            /** Use DECIMAL instead of FLOAT to store exact numeric values. */
            $table->decimal('amount', 20);
            $table->string('currency', 3);

            $table->decimal('sender_balance_before', 20);
            $table->decimal('sender_balance_after', 20);
            $table->decimal('recipient_balance_before', 20);
            $table->decimal('recipient_balance_after', 20);

            $table->timestamp('transaction_date');

            /** Restrict hard deletion. */
            $table->foreign('sender_account_id')->references('id')->on('accounts')->onDelete('restrict');
            $table->foreign('recipient_account_id')->references('id')->on('accounts')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_amount_positive CHECK (amount >= 0)');

        /** Make sure sender and recipient are different accounts. */
        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_sender_recipient_different CHECK (sender_account_id <> recipient_account_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
