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
        Schema::create('sparepart_masuk_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_masuk_id')->constrained('sparepart_masuk')->onDelete('cascade');
            $table->foreignId('sparepart_id')->nullable()->constrained('spareparts')->onDelete('restrict');

            $table->integer('jumlah_masuk');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_masuk_detail');
    }
};
