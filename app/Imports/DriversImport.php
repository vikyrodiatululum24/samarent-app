<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Driver;
use App\Models\Project;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class DriversImport implements ToCollection, WithHeadingRow
{
    public function collection($rows)
    {
        DB::transaction(function () use ($rows) {

            foreach ($rows as $row) {

                $email = strtolower(str_replace(' ', '',$row['name'])).$row['domain'];
                
                $user = User::where('email', $email)->first();
                if($user){
                    $user->delete();
                }

                $email = strtolower(str_replace(' ', '',$row['name'])).'@'.$row['domain'];

                if (User::where('email', $email)->first()) {
                    continue;
                }

                $project = Project::where('name', $row['project'])->first();
                $projectId = $project ? $project->id : null;
                $nik = Factory::create('id_ID')->numerify('################');

                $user = User::create([
                    'name' => $row['name'],
                    'email' => $email,
                    'password' => Hash::make($row['password']),
                    'role' => 'driver',
                ]);

                Driver::create([
                    'user_id' => $user->id,
                    'password' => $row['password'],
                    'nik' => $nik,
                    'alamat' => $row['alamat'],
                    'no_wa' => $row['no_wa'],
                    'project_id' => $projectId,
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'salary' => $row['salary'],
                ]);
            }

        });
    }
}
