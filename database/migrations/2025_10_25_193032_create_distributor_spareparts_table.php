<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('distributor_spareparts', function (Blueprint $table) {
            $table->id();

            // Basic distributor master data (Indonesian field names as requested)
            $table->string('nama_distributor'); // distributor name
            $table->string('kontak')->nullable(); // optional contact info
            $table->text('alamat')->nullable(); // optional address

            $table->timestamps();
            $table->softDeletes(); // enable soft deletes for master data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_spareparts');
    }
};
