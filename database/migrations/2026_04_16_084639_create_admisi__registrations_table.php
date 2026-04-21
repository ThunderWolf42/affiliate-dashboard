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
        Schema::create('admisi__registrations', function (Blueprint $table) {
            $table->string('no_registrasi')->primary();
            $table->integer('current_step')->default(1); // Tahap 1-8
            $table->string('nama_calon');
            $table->string('email');
            $table->string('no_hp');
            $table->timestamp('step_start_at')->useCurrent();// catat kapan tahap nya dimulai
            $table->timestamp('last_update_at')->useCurrent();
            $table->timestamps();

            $table->foreign('current_step')->references('step_number')->on('ref_tahapans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admisi__registrations');
    }
};
