<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramStudiSeeder extends Seeder
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

        ProgramStudi::truncate();

        foreach (range(1, 20) as $i) {
            array_push($data, [
                'nama_prodi' => Str::random(10),
				'kuota' => Str::random(10),
				'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $chunkeds = collect($data)->chunk(20);
        foreach ($chunkeds as $chunkData) {
            ProgramStudi::insert($chunkData->toArray());
        }
    }
}
