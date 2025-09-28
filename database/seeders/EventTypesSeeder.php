<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code'=>'goal', 'name'=>'Gol', 'category'=>'score'],
            ['code'=>'seven_meter_scored','name'=>'7m convertido','category'=>'score'],
            ['code'=>'seven_meter_missed','name'=>'7m perdido','category'=>'score'],

            ['code'=>'assist','name'=>'Assistência','category'=>'stat'],
            ['code'=>'save','name'=>'Defesa','category'=>'stat'],

            ['code'=>'yellow_card','name'=>'Cartão Amarelo','category'=>'discipline'],
            ['code'=>'red_card','name'=>'Cartão Vermelho','category'=>'discipline'],
            ['code'=>'blue_card','name'=>'Cartão Azul','category'=>'discipline'],
            ['code'=>'suspension_2min_start','name'=>'2 min início','category'=>'discipline'],
            ['code'=>'suspension_2min_end','name'=>'2 min fim','category'=>'discipline'],

            ['code'=>'timeout_home','name'=>'Tempo técnico casa','category'=>'timeout'],
            ['code'=>'timeout_away','name'=>'Tempo técnico fora','category'=>'timeout'],

            ['code'=>'substitution','name'=>'Substituição','category'=>'game'],
            ['code'=>'injury','name'=>'Lesão','category'=>'game'],
            ['code'=>'video_review','name'=>'Revisão de vídeo','category'=>'game'],
            ['code'=>'period_start','name'=>'Início do período','category'=>'game'],
            ['code'=>'period_end','name'=>'Fim do período','category'=>'game'],
        ];

        DB::table('event_types')->upsert($rows, ['code'], ['name','category']);
    }
}
