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
        Schema::create('ref_tahapans', function (Blueprint $table) {
            $table->integer('step_number')->primary(); // 1 sampai 8
            $table->string('step_name'); // Nama tahap (misal: "Tes Kesehatan")
            $table->integer('sla_days'); // Jatah waktu dalam hari (misal: 3 hari)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_tahapans');
    }
};
