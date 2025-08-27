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
        Schema::create('agenda_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
            $table->string('nama');
            $table->string('dinas_id', 255);
            $table->string('jabatan');
            $table->string('no_hp');
            $table->longText('gambar_ttd')->nullable();
            $table->string('qr_code')->nullable(); 
            $table->timestamps();
            
            // Add foreign key constraint manually
            $table->foreign('dinas_id')->references('dinas_id')->on('master_dinas')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_details');
    }
};
