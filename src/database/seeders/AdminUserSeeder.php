<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@bracmis.org'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        $this->command->info('Admin user created: admin@bracmis.org / password');
    }
}
