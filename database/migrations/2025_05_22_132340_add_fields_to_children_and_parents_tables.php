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
        // Tambah kolom di tabel children (anak)
        Schema::table('children', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nis')->nullable();
            $table->unsignedBigInteger('id_class')->nullable()->change(); // pastikan sudah ada, jika belum tambahkan
            $table->string('tahun_ajaran')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
        });

        // Tambah kolom di tabel parents (ortu)
        Schema::table('parents', function (Blueprint $table) {
            $table->string('alamat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'tanggal_lahir', 'nis', 'tahun_ajaran', 'gender']);
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn('alamat');
        });
    }
};
