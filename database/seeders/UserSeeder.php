<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      User::updateOrCreate(
        ['email' => 'alice@example.test'],
        [
          'name' => 'Alice',
          'password' => Hash::make('password'),
          'balance' => 1000.00,
        ]
      );

      User::updateOrCreate(
        ['email' => 'bob@example.test'],
        [
          'name' => 'Bob',
          'password' => Hash::make('password'),
          'balance' => 250.00,
        ]
      );

      User::updateOrCreate(
        ['email' => 'nauman@example.test'],
        [
          'name' => 'Nauman',
          'password' => Hash::make('password'),
          'balance' => 500.00,
        ]
      );
    }
}
