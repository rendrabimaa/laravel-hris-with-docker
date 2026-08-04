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
        Schema::create('salary_batches', function(Blueprint $table) {
            $table->id();
            $table->string('batch_name');
            $table->string('periode');
            $table->string('hrd_name');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });

        Schema::create('salary_items', function(Blueprint $table) {
            $table->id();
            $table->foreignId('salary_batch_id')->constrained()->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('nip');
            $table->string('jabatan')->nullable();
            $table->string('npwp')->nullable();

            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('tunjangan_loyalitas', 15, 2)->default(0);
            $table->decimal('tunjangan_jabatan', 15, 2)->default(0);
            $table->decimal('lembur', 15, 2)->default(0);
            $table->decimal('insentif', 15, 2)->default(0);
            $table->decimal('uang_makan', 15, 2)->default(0);
            $table->decimal('jml_pendapatan', 15, 2)->default(0);

            $table->decimal('bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('bpjs_tk', 15, 2)->default(0);
            $table->decimal('kedisiplinan', 15, 2)->default(0);
            $table->decimal('jml_potongan', 15, 2)->default(0);

            $table->decimal('gaji_bersih', 15, 2)->default(0);

            $table->string('pdf_path')->nullable();
            $table->enum('status', ['pending', 'processing', 'generated', 'sent', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_items');
        Schema::dropIfExists('salary_batches');
    }
};
