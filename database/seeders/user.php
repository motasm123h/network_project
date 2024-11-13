<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User as ModelsUser;
// use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class user extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = ModelsUser::create([
            'name' => 'motasm',
            'email' => 'motasm2@gmail.com',
            'password' => Hash::make('123456'),
        ]);
    }
}
