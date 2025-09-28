<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // LOOKUPS
            PlayerPositionsSeeder::class,
            StaffRolesSeeder::class,
            CompetitionTypesSeeder::class,
            EventTypesSeeder::class,

            // CORE
            TeamsSeeder::class,
            PlayersSeeder::class,
            CompetitionsSeeder::class,
            GroupsSeeder::class,        // se usar grupos

            // PARTIDAS
            MatchesSeeder::class,

            // (Opcional) estatísticas/standings seeds aqui
        ]);
    }
}
