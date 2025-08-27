<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique(); // Sertifikat raqami
            $table->string('name'); // Sertifikat nomi
            $table->text('description')->nullable(); // Qo'shimcha ma'lumot
            $table->date('start_date'); // Boshlanish sanasi
            $table->date('end_date'); // Tugash sanasi
            $table->string('pdf_file'); // PDF fayl yo'li
            $table->string('image_file')->nullable(); // Rasm fayl yo'li
            $table->boolean('is_active')->default(true); // Faol/Nofaol holati
            $table->timestamps();
            $table->softDeletes(); // O'chirilgan sana
        });

        // Maxsulotlar jadvalidagi certificate_id ustunini qo'shamiz
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('certificate_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        // Avval foreign key ni o'chiramiz
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['certificate_id']);
            $table->dropColumn('certificate_id');
        });

        Schema::dropIfExists('certificates');
    }
}
