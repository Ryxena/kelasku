<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        School::create([
            "name" => "SMK 1 Bizzare"
        ]);
        School::create([
            "name" => "SMK 2 Bizzare"
        ]);
        School::create([
            "name" => "SMK 3 Bizzare"
        ]);
        School::create([
            "name" => "SMK 4 Bizzare"
        ]);
        School::create([
            "name" => "SMK 5 Bizzare"
        ]);
        School::create([
            "name" => "SMK 6 Bizzare"
        ]);
    }
}
