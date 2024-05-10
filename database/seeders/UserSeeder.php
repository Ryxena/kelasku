<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "admin",
            "phone" => "00000000",
            "profile" => "",
            "role" => "admin",
            "password" => Hash::make("12345678"),
            "school_id" => "1"
        ]);
        User::create([
            "name" => "Rubben",
            "phone" => "11111111",
            "profile" => "",
            "password" => Hash::make("12345678"),
            "school_id" => "1"
        ]);
        User::create([
            "name" => "John",
            "phone" => "22222222",
            "profile" => "",
            "password" => Hash::make("12345678"),
            "school_id" => "2"
        ]);
        User::create([
            "name" => "Doe",
            "phone" => "33333333",
            "profile" => "",
            "password" => Hash::make("12345678"),
            "school_id" => "3"
        ]);
        User::create([
            "name" => "David",
            "phone" => "44444444",
            "profile" => "",
            "password" => Hash::make("12345678"),
            "school_id" => "4"
        ]);
        User::create([
            "name" => "Abraham",
            "phone" => "55555555",
            "profile" => "",
            "password" => Hash::make("12345678"),
            "school_id" => "5"
        ]);
        User::create([
            "name" => "Joseph",
            "phone" => "66666666",
            "profile" => "",
            "password" => Hash::make("12345678"),
            "school_id" => "6"
        ]);
    }
}
