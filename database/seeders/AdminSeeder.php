<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Desy',
                'username' => 'admindesy',
                'email' => 'admindesy@wafaindonesia.com',
            ],
            [
                'name' => 'Putri',
                'username' => 'adminputri',
                'email' => 'adminputri@wafaindonesia.com',
            ],
            [
                'name' => 'Budi',
                'username' => 'adminbudi',
                'email' => 'adminbudi@wafaindonesia.com',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'username' => $admin['username'], // penting karena NOT NULL
                    'password' => Hash::make('Admin12345!'),
                ]
            );
        }
    }
}
