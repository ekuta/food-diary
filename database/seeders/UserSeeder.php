<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => config('mail.from.name'),
            'email' => config('mail.from.address'),
            'email_verified_at' => now(),
            'password' => Hash::make('J2z28^Bd%X%3Aj'),
        ]);
    }
}
