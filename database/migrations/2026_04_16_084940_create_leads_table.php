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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('no_registrasi')->nullable();
            $table->string('lead_name');
            $table->string('email');
            $table->string('wa_number');
            $table->integer('jurusan_id')->nullable();
            $table->string('status')->default('pending');
            $table->string('telegram_chat_id')->nullable();
            $table->string('batch_name')->nullable();


            $table->decimal('total_tagihan', 12, 2)->default(0); // Total tagihan uang pangkal + S1
            $table->decimal('total_dibayar', 12, 2)->default(0); // Nominal yang dicicil/dibayar maba
            $table->boolean('is_refund_case')->default(false);   // Status jika maba batal/mundur

            $table->integer('batch_number')->default(1);
            $table->integer('batch_year')->default(2026);

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
