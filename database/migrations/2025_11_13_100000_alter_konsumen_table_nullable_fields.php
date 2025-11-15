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
        Schema::table('konsumen', function (Blueprint $table) {
            $table->string('alamat')->nullable()->change();
            $table->string('telepon')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsumen', function (Blueprint $table) {
            $table->string('alamat')->nullable(false)->change();
            $table->string('telepon')->nullable(false)->change();
        });
    }
};
