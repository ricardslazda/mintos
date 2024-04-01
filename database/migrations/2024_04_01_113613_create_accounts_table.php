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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');

            /** Use DECIMAL instead of FLOAT to store exact numeric values. */
            $table->decimal('balance', 20);

            $table->string('currency', 3);
            $table->timestamps();
            $table->softDeletes();

            /** Restrict hard deletion. */
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE accounts ADD CONSTRAINT chk_balance_positive CHECK (balance >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
