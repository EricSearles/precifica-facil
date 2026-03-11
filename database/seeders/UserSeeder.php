<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Maria',
            'email' => 'admin@precificafacil.com',
            'password' => Hash::make('123456'),
            'company_id' => 1,
            'role' => 'owner',
            'is_owner' => true,
        ]);
    }
}