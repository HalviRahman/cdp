<?php

namespace Database\Seeders;

use App\Models\Proposal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProposalSeeder extends Seeder
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

        Proposal::truncate();

        foreach (range(1, 20) as $i) {
            array_push($data, [
                'id_kelompok' => Str::random(10),
				'judul_proposal' => Str::random(10),
				'file_proposal' => Str::random(10),
				'tgl_upload' => $faker->date("Y-m-d", $max = date("Y-m-d")), // ganti method fakernya sesuai kebutuhan
				'status' => $faker->numberBetween(0,1000), // ganti method fakernya sesuai kebutuhan
				'verifikator' => Str::random(10),
				'keterangan' => Str::random(10),
				'tgl_verifikasi' => $faker->date("Y-m-d", $max = date("Y-m-d")), // ganti method fakernya sesuai kebutuhan
				'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $chunkeds = collect($data)->chunk(20);
        foreach ($chunkeds as $chunkData) {
            Proposal::insert($chunkData->toArray());
        }
    }
}
