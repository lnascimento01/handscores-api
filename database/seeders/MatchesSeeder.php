<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MatchesSeeder extends Seeder
{
    public function run(): void
    {
        $compId  = DB::table('competitions')->where('name','Paranaense 2025')->value('id');
        $thalia  = DB::table('teams')->where('slug','thalia')->value('id');
        $carioca = DB::table('teams')->where('slug','carioca')->value('id');

        $start = Carbon::now()->addDay()->setTime(20,0); // amanhã 20:00

        $rows = [
            [
              'competition_id'=>$compId,
              'home_team_id'=>$thalia,
              'away_team_id'=>$carioca,
              'start_at'=>$start->toDateTimeString(),
              'status'=>'scheduled',
              'home_score'=>0,
              'away_score'=>0,
              'period'=>0,
              'clock_seconds'=>0,
              'meta'=>json_encode([]),
            ],
        ];

        foreach ($rows as $r) {
            DB::table('matches')->insertOrIgnore($r);
        }
    }
}
