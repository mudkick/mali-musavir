<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bildirimler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mukellef_id')->constrained('mukellefs')->cascadeOnDelete();
            $table->string('sablon_turu');
            $table->text('mesaj_metni');
            $table->string('kanal');
            $table->string('durum');
            $table->timestamp('gonderim_tarihi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bildirimler');
    }
};
