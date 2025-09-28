<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsSeeder extends Seeder
{
    public function run(): void
    {
        $compId = DB::table('competitions')->where('name','Paranaense 2025')->value('id');

        DB::table('groups')->updateOrInsert(['competition_id'=>$compId,'name'=>'Grupo A'], ['order'=>1]);
        DB::table('groups')->updateOrInsert(['competition_id'=>$compId,'name'=>'Grupo B'], ['order'=>2]);

        $gA = DB::table('groups')->where(['competition_id'=>$compId,'name'=>'Grupo A'])->value('id');
        $thalia = DB::table('teams')->where('slug','thalia')->value('id');
        $cari   = DB::table('teams')->where('slug','carioca')->value('id');

        DB::table('group_team')->updateOrInsert(['group_id'=>$gA,'team_id'=>$thalia], ['order'=>1]);
        DB::table('group_team')->updateOrInsert(['group_id'=>$gA,'team_id'=>$cari], ['order'=>2]);
    }
}
