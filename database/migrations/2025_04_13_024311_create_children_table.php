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
            Schema::create('children', function (Blueprint $table) {
                $table->id('id_child');
                $table->string('name');
                $table->unsignedBigInteger('id_class');
                $table->foreign('id_class')->references('id_class')->on('classes')->onDelete('cascade');    
                $table->unsignedBigInteger('id_parent');
                $table->foreign('id_parent')->references('id_parent')->on('parents')->onDelete('cascade');
                $table->timestamps();
            });
            
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('children');
        }
    };
