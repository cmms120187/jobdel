<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get positions
        $staff = Position::where('name', 'Staff')->first();
        $leader = Position::where('name', 'Leader')->first();
        $supervisor = Position::where('name', 'Supervisor')->first();
        $astManager = Position::where('name', 'Ast Manager')->first();
        $manager = Position::where('name', 'Manager')->first();
        $gm = Position::where('name', 'GM')->first();
        $superuser = Position::where('name', 'Superuser')->first();

        $users = [
            // Supervisor
            ['nik' => '16132', 'name' => 'EMILIA DWI LESTARI', 'position' => $supervisor],
            ['nik' => '53493', 'name' => 'SYAHRIL DIMASTA PERANGIN ANGIN', 'position' => $supervisor],
            
            // Staff
            ['nik' => '34149', 'name' => 'SUYATMI', 'position' => $staff],
            ['nik' => '34727', 'name' => 'CHAERUL RIZAL', 'position' => $staff],
            ['nik' => '36747', 'name' => 'DEVI PUSPITA SARI', 'position' => $staff],
            ['nik' => '38150', 'name' => 'AJI PURWANTO', 'position' => $staff],
            ['nik' => '44666', 'name' => 'MUSLIM', 'position' => $staff],
            ['nik' => '46178', 'name' => 'JENTINA SIMBOLON', 'position' => $staff],
            ['nik' => '48043', 'name' => 'PARWANTI', 'position' => $staff],
            ['nik' => '49044', 'name' => 'AMANDA RIZKY', 'position' => $staff],
            ['nik' => '50269', 'name' => 'RENCY PUTRI YANI', 'position' => $staff],
            ['nik' => '52326', 'name' => 'PRAYOGA RANGGANA.S.H', 'position' => $staff],
            ['nik' => '46733', 'name' => 'INDRA SATRIA PRASTA', 'position' => $staff],
            
            // Leader
            ['nik' => '37097', 'name' => 'FAIZAL ZULMI', 'position' => $leader],
            ['nik' => '27306', 'name' => 'LIA MULYANINGSIH', 'position' => $leader],
            
            // Ast Manager
            ['nik' => '48715', 'name' => 'WAHID NURCIPTO', 'position' => $astManager],
            
            // Manager
            ['nik' => '29076', 'name' => 'MAS GOFUR,AMD', 'position' => $manager],
            
            // GM
            ['nik' => '31955', 'name' => 'CHANDRA MULIA PERKASA', 'position' => $gm],
            
            // Superuser
            ['nik' => '12345', 'name' => 'administrator', 'position' => $superuser, 'email' => 'wahid@tpmcmms.id'],
        ];

        foreach ($users as $userData) {
            // Use provided email or generate from name
            if (isset($userData['email'])) {
                $email = $userData['email'];
            } else {
                $email = User::generateEmailFromName($userData['name']);
                
                // Check if email already exists, if yes, add number
                $counter = 1;
                $originalEmail = $email;
                while (User::where('email', $email)->exists()) {
                    $email = str_replace('@pai.pratama.net', $counter . '@pai.pratama.net', $originalEmail);
                    $counter++;
                }
            }
            
            // Password = NIK (NIK = Username = Password)
            $password = Hash::make($userData['nik']);

            User::create([
                'nik' => $userData['nik'],
                'name' => $userData['name'],
                'email' => $email,
                'password' => $password,
                'position_id' => $userData['position']->id,
            ]);
        }
    }
}
