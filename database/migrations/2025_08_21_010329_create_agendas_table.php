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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('dinas_id', 50);
            $table->string('nama_agenda');
            $table->date('tanggal_agenda');
            $table->string('nama_koordinator');
            $table->string('link_acara')->unique();
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
        Schema::dropIfExists('agendas');
    }
};
