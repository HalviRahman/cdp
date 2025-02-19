<?php

namespace App\Imports;

use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class UserImport implements ToCollection, WithHeadingRow
{

    /**
     * user repository
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->userRepository = new UserRepository;
    }

    /**
     * To collection
     *
     * @return void
     */
    public function collection(Collection $rows)
    {
        $dateTime = date('Y-m-d H:i:s');
        foreach ($rows->chunk(30) as $chunkData) {
            $insertData = $chunkData->map(function ($item) use ($dateTime) {
                $prodiString = $item['prodi']; // Move this inside the map function
                $prodiArray = explode(',', $prodiString); // Split string into array

                return [
                    'name'         => $item['nama'],
                    'nip'          => $item['nip'],
                    'prodi'        => json_encode($prodiArray), // Corrected bracket
                    'email'        => $item['email'],
                    'password'     => $item['password'],
                    // 'phone_number' => $item['no_hp'],
                    // 'birth_date'   => $item['tanggal_lahir'],
                    // 'address'      => $item['alamat'],
                    'created_at'   => $dateTime,
                    'updated_at'   => $dateTime,
                    // 'avatar',
                    // 'email_verified_at',
                    // 'last_login',
                    // 'email_token',
                    // 'verification_code',
                    // 'is_locked',
                    // 'last_password_change',
                    // 'twitter_id',
                ];
            })->toArray();
            $this->userRepository->insert($insertData);
        }

        foreach ($rows->chunk(30) as $chunkData) {
            $insertData = $chunkData->map(function ($item) {
                $roles = explode(',', $item['roles']);
                $user  = $this->userRepository->findByEmail($item['email']);
                $this->userRepository->syncRoles($user, $roles);
            });
        }
    }
}
