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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('lead_name');
            $table->string('email')->nullable();
            $table->string('wa_number');
            $table->foreignId('jurusan_id')->nullable()->constrained('ref_jurusans');
            $table->string('no_registrasi')->nullable(); // Hilangkan unique, buat nullable
            $table->enum('status', ['pending', 'active', 'stalled', 'out'])->default('pending');
            $table->timestamps();
        });
        // Schema::create('leads', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa affiliatenya
        //     $table->string('no_registrasi')->unique(); // FK ke sim_admisi_pendaftaran
        //     $table->string('lead_name');
        //     $table->string('wa_number');
        //     $table->enum('status', ['active', 'stalled', 'drop-out'])->default('active');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
