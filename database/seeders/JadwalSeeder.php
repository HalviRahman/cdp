<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data  = [];
        $faker = \Faker\Factory::create('id_ID');
        $now   = date('Y-m-d H:i:s');

        Jadwal::truncate();

        foreach (range(1, 20) as $i) {
            array_push($data, [
                'tgl_mulai' => $faker->date("Y-m-d", $max = date("Y-m-d")), // ganti method fakernya sesuai kebutuhan
				'tgl_selesai' => $faker->date("Y-m-d", $max = date("Y-m-d")), // ganti method fakernya sesuai kebutuhan
				'keterangan' => Str::random(10),
				'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $chunkeds = collect($data)->chunk(20);
        foreach ($chunkeds as $chunkData) {
            Jadwal::insert($chunkData->toArray());
        }
    }
}
