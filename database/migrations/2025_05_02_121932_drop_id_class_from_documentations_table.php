<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
    Schema::table('documentations', function (Blueprint $table) {
        $table->dropColumn('id_class');
    });
    }


    public function down(): void {
        Schema::table('documentations', function (Blueprint $table) {
            $table->unsignedBigInteger('id_class')->nullable();
            $table->foreign('id_class')->references('id_class')->on('classes')->onDelete('cascade');
        });
    }
};
