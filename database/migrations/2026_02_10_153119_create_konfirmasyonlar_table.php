<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfirmasyonlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mukellef_id')->constrained('mukellefs')->cascadeOnDelete();
            $table->string('donem');
            $table->text('mesaj_metni');
            $table->timestamp('gonderim_tarihi');
            $table->string('yanit')->nullable();
            $table->unsignedInteger('beklenen_belge_sayisi')->nullable();
            $table->timestamp('yanit_tarihi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfirmasyonlar');
    }
};
