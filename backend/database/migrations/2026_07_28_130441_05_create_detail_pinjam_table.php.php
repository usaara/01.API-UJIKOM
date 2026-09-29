<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('detail_pinjam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelate();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnDelate();
            $table->integer('jumlah')->default(1);
            $table->timestamps();
        });
        
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
