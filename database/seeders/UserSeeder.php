<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@smarttable.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create Editor User
        $editor = User::create([
            'name' => 'Editor User',
            'email' => 'editor@smarttable.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $editor->assignRole('editor');
    }
}
