<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@wms.local'],
            [
                'name' => 'Administrator',
                'email' => 'admin@wms.local',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin->value,
            ]
        );
    }
}
