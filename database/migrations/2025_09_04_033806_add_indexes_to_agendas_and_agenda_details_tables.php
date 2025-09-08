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
        Schema::table('agendas', function (Blueprint $table) {
            $table->index('tanggal_agenda');
            $table->index('link_active');
        });

        Schema::table('agenda_details', function (Blueprint $table) {
            $table->index('agenda_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropIndex(['tanggal_agenda']);
            $table->dropIndex(['link_active']);
        });

        Schema::table('agenda_details', function (Blueprint $table) {
            $table->dropIndex(['agenda_id']);
        });
    }
};
