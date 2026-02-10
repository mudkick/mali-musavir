<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beyanname_tipleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mukellef_id')->constrained('mukellefs')->cascadeOnDelete();
            $table->string('tip');
            $table->string('periyot');
            $table->unsignedTinyInteger('son_gun');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beyanname_tipleri');
    }
};
