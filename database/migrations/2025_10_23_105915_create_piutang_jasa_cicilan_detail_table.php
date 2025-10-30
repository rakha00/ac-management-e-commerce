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
        Schema::create('piutang_jasa_cicilan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_jasa_id')->constrained('piutang_jasa')->onDelete('cascade');
            $table->integer('nominal_cicilan')->default(0);
            $table->date('tanggal_cicilan');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutang_jasa_cicilan_detail');
    }
};
