<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Admin Demo',
                'password' => Hash::make('admindemo'),
                'role' => UserRole::Admin,
            ]
        );
        User::updateOrCreate(
            ['email' => 'restricted@demo.test'],
            [
                'name' => 'Restricted Demo',
                'password' => Hash::make('restricteddemo'),
                'role' => UserRole::Restricted,
            ]
        );
    }
}
