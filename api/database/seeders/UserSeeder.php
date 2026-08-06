<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Teste User',
            'username' => 'testeuser',
            'email' => 'teste@example.com',
            'password' => 'secret123',
        ]);
    }
}
