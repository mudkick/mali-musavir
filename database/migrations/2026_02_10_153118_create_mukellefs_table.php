<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mukellefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ad');
            $table->string('vkn', 10)->nullable();
            $table->string('tckn', 11)->nullable();
            $table->string('tur');
            $table->string('telefon')->nullable();
            $table->string('email')->nullable();
            $table->string('iletisim_kanali');
            $table->string('telegram_chat_id')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mukellefs');
    }
};
