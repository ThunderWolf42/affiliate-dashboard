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
            $table->integer('current_step')->default(1); // Tahap 1-4 sesuai aturan baru Admisi
            $table->string('nama_calon');
            $table->string('email');
            $table->string('no_hp');
            $table->foreignId('jurusan_id')->constrained('ref_jurusans');
            $table->timestamp('step_start_at')->useCurrent(); // Catat kapan tahap nya dimulai
            $table->timestamp('last_update_at')->useCurrent();

            
            $table->decimal('total_tagihan', 12, 2)->default(0); // Total tagihan (Uang Pangkal + Kuliah S1)
            $table->decimal('total_dibayar', 12, 2)->default(0); // Nominal yang sudah dicicil/dibayar maba

            $table->integer('batch_number')->default(1); // Menyimpan angka 1, 2, 3, dst
            $table->integer('batch_year')->default(2026);  // Menyimpan tahun angka 2026

            $table->timestamps();

            // Relasi foreign key ke tabel master tahapan linear
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
