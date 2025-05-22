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
        Schema::table('children', function (Blueprint $table) {
            // Hapus kolom tahun_ajaran lama
            $table->dropColumn('tahun_ajaran');
            // Tambahkan kolom id_tahun_ajaran
            $table->unsignedBigInteger('id_tahun_ajaran')->nullable()->after('id_class');
            // Foreign key (opsional, jika ingin relasi)
            $table->foreign('id_tahun_ajaran')->references('id')->on('tahun_ajarans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            // Kembalikan kolom tahun_ajaran
            $table->string('tahun_ajaran')->nullable();
            // Hapus foreign key dan kolom id_tahun_ajaran
            $table->dropForeign(['id_tahun_ajaran']);
            $table->dropColumn('id_tahun_ajaran');
        });
    }
};
