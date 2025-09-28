<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompetitionsSeeder extends Seeder
{
    public function run(): void
    {
        $leagueTypeId = DB::table('competition_types')->where('name','League')->value('id');

        DB::table('competitions')->updateOrInsert(
            ['name'=>'Paranaense 2025', 'season'=>'2025'],
            ['type_id'=>$leagueTypeId, 'country'=>'BR', 'meta'=>json_encode([])]
        );
    }
}
