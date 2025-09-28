<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name'=>'Thalia',  'short_name'=>'THA', 'country'=>'BR', 'city'=>'Curitiba', 'slug'=>Str::slug('Thalia')],
            ['name'=>'Carioca', 'short_name'=>'CAR', 'country'=>'BR', 'city'=>'Curitiba', 'slug'=>Str::slug('Carioca')],
        ];

        foreach ($rows as $r) {
            DB::table('teams')->updateOrInsert(['slug'=>$r['slug']], $r + ['colors'=>json_encode(['primary'=>'#F53D68'])]);
        }
    }
}
