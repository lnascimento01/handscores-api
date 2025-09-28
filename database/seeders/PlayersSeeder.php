<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayersSeeder extends Seeder
{
    public function run(): void
    {
        $thaliaId = DB::table('teams')->where('slug','thalia')->value('id');
        $cariId   = DB::table('teams')->where('slug','carioca')->value('id');

        $rows = [
            ['first_name'=>'Matias','last_name'=>'Gitzel','number'=>1,'nationality'=>'BR','team_id'=>$thaliaId,'position_id'=>DB::table('player_positions')->where('code','GK')->value('id')],
            ['first_name'=>'Leandro','last_name'=>'Nascimento','number'=>23,'nationality'=>'BR','team_id'=>$thaliaId,'position_id'=>DB::table('player_positions')->where('code','LB')->value('id')],
            ['first_name'=>'Carlos','last_name'=>'Souza','number'=>9,'nationality'=>'BR','team_id'=>$cariId,'position_id'=>DB::table('player_positions')->where('code','PV')->value('id')],
        ];

        foreach ($rows as $r) {
            DB::table('players')->updateOrInsert(
                ['team_id'=>$r['team_id'],'number'=>$r['number']],
                $r
            );
        }
    }
}
