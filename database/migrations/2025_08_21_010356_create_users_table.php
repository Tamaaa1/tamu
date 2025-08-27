<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); 
            $table->string('username')->unique(); 
            $table->string('name')->nullable();
            $table->string('password'); 
            $table->string('role')->default('admin'); 
            $table->string('dinas_id', 10)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('dinas_id')->references('dinas_id')->on('master_dinas')->onDelete('set null');
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
