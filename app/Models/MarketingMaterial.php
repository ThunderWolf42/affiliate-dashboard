<?php

namespace App\Models;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class MarketingMaterial extends Model
{
    protected $fillable = [
        'title',
        'description',
        'download_link'
    ];

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
}
