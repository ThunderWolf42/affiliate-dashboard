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
        Schema::create('marketing_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: "Pamflet Beasiswa Ukrida 2026"
            $table->text('description')->nullable(); // Contoh: "Share ke grup WA"
            $table->string('download_link'); // Link cloud storage (Google Drive/Dropbox)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_materials');
    }
};
