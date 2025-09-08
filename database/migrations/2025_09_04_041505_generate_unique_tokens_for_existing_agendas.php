<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Agenda;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            //
        });

        // Generate unique tokens for existing agendas without tokens
        $agendas = Agenda::whereNull('unique_token')->get();
        foreach ($agendas as $agenda) {
            $agenda->unique_token = Agenda::generateUniqueToken();
            $agenda->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            //
        });
    }
};
