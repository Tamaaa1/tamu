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
        // Index untuk tabel agendas
        Schema::table('agendas', function (Blueprint $table) {
            $table->index(['tanggal_agenda', 'link_active']); // Composite index untuk query yang sering
        });

        // Index untuk tabel agenda_details
        Schema::table('agenda_details', function (Blueprint $table) {
            $table->index('created_at'); // Untuk filter created_at
            $table->index(['agenda_id', 'created_at']); // Composite untuk filter agenda + tanggal
            $table->index('dinas_id'); // Untuk join dengan master_dinas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index untuk tabel agendas
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropIndex(['tanggal_agenda']);
            $table->dropIndex(['link_active']);
            $table->dropIndex(['tanggal_agenda', 'link_active']);
        });

        // Drop index untuk tabel agenda_details
        Schema::table('agenda_details', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['agenda_id', 'created_at']);
            $table->dropIndex(['dinas_id']);
        });
    }
};
