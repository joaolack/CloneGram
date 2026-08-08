<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'João Gabriel',
                'username' => 'joaolack',
                'email' => 'joao@example.com',
                'bio' => 'Desenvolvedor e estudante.',
            ],
            [
                'name' => 'Victor Raphael',
                'username' => 'victor',
                'email' => 'victor@example.com',
                'bio' => 'Professor e Devops.',
            ],
            [
                'name' => 'Luiz Henrique',
                'username' => 'luizhc',
                'email' => 'luiz@example.com',
                'bio' => 'Tecnologia e música.',
            ],
            [
                'name' => 'Ana Santos',
                'username' => 'ana',
                'email' => 'ana@example.com',
                'bio' => 'Fotografia, viagens e café.',
            ],
            [
                'name' => 'Mauricio Cescon',
                'username' => 'cescon',
                'email' => 'cescontos@example.com',
                'bio' => 'Designer e produtor de conteúdo.',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                ...$user,
                'password' => Hash::make('password'),
                'avatar_path' => null,
            ]);
        }
    }
}
