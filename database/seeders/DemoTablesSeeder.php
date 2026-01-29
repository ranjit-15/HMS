<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoTablesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $grid = [
            ['name' => 'T-1', 'x' => 1, 'y' => 1, 'capacity' => 2, 'is_active' => true],
            ['name' => 'T-2', 'x' => 2, 'y' => 1, 'capacity' => 2, 'is_active' => true],
            ['name' => 'T-3', 'x' => 3, 'y' => 1, 'capacity' => 4, 'is_active' => true],
            ['name' => 'T-4', 'x' => 1, 'y' => 2, 'capacity' => 1, 'is_active' => true],
            ['name' => 'T-5', 'x' => 2, 'y' => 2, 'capacity' => 1, 'is_active' => true],
            ['name' => 'T-6', 'x' => 3, 'y' => 2, 'capacity' => 3, 'is_active' => true],
        ];

        foreach ($grid as $row) {
            DB::table('tables')->updateOrInsert(
                ['x' => $row['x'], 'y' => $row['y']],
                array_merge($row, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }
}
