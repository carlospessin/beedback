<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Beedback Admin',
            'email' => 'beedback@beedback.com',
            'password' => Hash::make('12345678'),
            'role_id' => 1,
        ]);
    
        User::factory()->create([
            'name' => 'Beedback User',
            'email' => 'user@beedback.com',
            'password' => Hash::make('12345678'),
            'role_id' => 2,
        ]);
    }
}
