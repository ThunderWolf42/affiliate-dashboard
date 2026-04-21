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
        Schema::create('u_a_a__mahasiswas', function (Blueprint $table) {
            $table->string('nim')->primary();
            $table->string('full_name');
            $table->string('email_civitas')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('has_tuition_debt')->default(false);
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_a_a__mahasiswas');
    }
};
