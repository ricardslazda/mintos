<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('client_key');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('client_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
