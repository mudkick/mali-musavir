<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('belgeler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mukellef_id')->constrained('mukellefs')->cascadeOnDelete();
            $table->string('gorsel_path');
            $table->json('extracted_data')->nullable();
            $table->string('durum');
            $table->float('confidence')->nullable();
            $table->string('belge_turu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('belgeler');
    }
};
