<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            $table->dropColumn('ketua_email');
            $table->string('peran')->after('anggota_email');
        });
    }

    public function down()
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            $table->string('ketua_email')->after('id_kelompok');
            $table->dropColumn('peran');
        });
    }
};
