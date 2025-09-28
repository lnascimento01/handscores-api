<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffRolesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name'=>'Coach'],
            ['name'=>'Assistant Coach'],
            ['name'=>'Goalkeeping Coach'],
            ['name'=>'Physiotherapist'],
            ['name'=>'Analyst'],
            ['name'=>'Team Manager'],
        ];

        DB::table('staff_roles')->upsert($rows, ['name'], []);
    }
}
