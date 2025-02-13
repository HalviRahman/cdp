<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
			$table->string('id_kelompok', 191);
			$table->string('judul_proposal', 500);
			$table->string('file_proposal', 255);
			$table->date('tgl_upload');
			$table->tinyInteger('status');
			$table->string('verifikator', 255);
			$table->string('keterangan', 255);
			$table->date('tgl_verifikasi');
			$table->timestamps();
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporans');
    }
}
