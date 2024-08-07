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
        Schema::create('daftar_digital_marketing', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->string('nisn')->nullable();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('jk');
            $table->string('alamat');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('agama');
            $table->string('status');
            $table->string('nama_ibu');
            $table->string('nama_ayah');
            $table->string('telepon');
            $table->string('email');
            $table->string('paket');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_digital_marketing', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
