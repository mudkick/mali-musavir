<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beyanname_takvim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mukellef_id')->constrained('mukellefs')->cascadeOnDelete();
            $table->foreignId('beyanname_tipi_id')->constrained('beyanname_tipleri')->cascadeOnDelete();
            $table->string('donem');
            $table->date('son_tarih');
            $table->string('durum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beyanname_takvim');
    }
};
