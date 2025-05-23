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
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['id_activity']);
            $table->dropForeign(['id_teacher']);
            $table->dropForeign(['id_document']);
            $table->dropColumn(['id_activity', 'id_document']);

            $table->unsignedBigInteger('id_class')->nullable()->after('id_teacher');
            $table->unsignedBigInteger('id_parent')->nullable()->after('id_class');
            $table->unsignedBigInteger('id_child')->nullable()->after('id_parent');
            $table->string('file')->nullable()->after('report_content');

            $table->foreign('id_teacher')->references('id_teacher')->on('teachers');
            $table->foreign('id_class')->references('id_class')->on('classes');
            $table->foreign('id_parent')->references('id_parent')->on('parents');
            $table->foreign('id_child')->references('id_child')->on('children');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['id_class']);
            $table->dropForeign(['id_parent']);
            $table->dropForeign(['id_child']);
            $table->dropColumn(['id_class', 'id_parent', 'id_child', 'file']);
            // Tambahkan kembali kolom lama jika perlu
        });
    }
};
