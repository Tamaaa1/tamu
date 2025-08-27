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
        Schema::table('agenda_details', function (Blueprint $table) {
            // Ubah qr_code menjadi longText untuk menyimpan base64
            $table->longText('qr_code')->nullable()->change();
            
            // Ubah gambar_ttd menjadi longText untuk menyimpan base64 (jika belum)
            $table->longText('gambar_ttd')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_details', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->change();
            $table->string('gambar_ttd')->nullable()->change();
        });
    }
};
