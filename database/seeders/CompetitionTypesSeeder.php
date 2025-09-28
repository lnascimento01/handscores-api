<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompetitionTypesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'League', 'description' => 'Pontos corridos'],
            ['name' => 'Cup', 'description' => 'Mata-mata'],
            ['name' => 'Groups', 'description' => 'Grupos'],
            ['name' => 'Groups + Playoff', 'description' => 'Grupos + Playoff'],
            ['name' => 'Friendly', 'description' => 'Amistoso'],
            ['name' => 'International', 'description' => 'Interclubes/Seleções'],
        ];

        DB::table('competition_types')->upsert($rows, ['name'], ['description']);
    }
}
