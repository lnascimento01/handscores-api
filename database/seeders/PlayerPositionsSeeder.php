<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerPositionsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code'=>'GK','name'=>'Goalkeeper'],
            ['code'=>'LW','name'=>'Left Wing'],
            ['code'=>'LB','name'=>'Left Back'],
            ['code'=>'CB','name'=>'Center Back'],
            ['code'=>'RB','name'=>'Right Back'],
            ['code'=>'RW','name'=>'Right Wing'],
            ['code'=>'PV','name'=>'Pivot'],
        ];

        DB::table('player_positions')->upsert($rows, ['code'], ['name']);
    }
}
