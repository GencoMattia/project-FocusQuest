<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Jane',
            'surname' => 'Doe',
            'email' => 'janedoe@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}

