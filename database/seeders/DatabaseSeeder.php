<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create specific admin user
        User::create([
            'name' => 'Taye Animaw',
            'email' => 'tayeanimaw7@gmail.com',
            'phone' => '0912345678',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: tayeanimaw7@gmail.com');
        $this->command->info('Password: 12345678');
    }
}
