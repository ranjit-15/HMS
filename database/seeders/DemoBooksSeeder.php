<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoBooksSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
            ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'isbn' => '9780132350884', 'copies_total' => 5, 'copies_available' => 5, 'is_active' => true],
            ['title' => 'The Pragmatic Programmer', 'author' => 'Andrew Hunt', 'isbn' => '9780201616224', 'copies_total' => 4, 'copies_available' => 4, 'is_active' => true],
            ['title' => 'Design Patterns', 'author' => 'GoF', 'isbn' => '9780201633610', 'copies_total' => 3, 'copies_available' => 3, 'is_active' => true],
            ['title' => 'Introduction to Algorithms', 'author' => 'CLRS', 'isbn' => '9780262046305', 'copies_total' => 2, 'copies_available' => 2, 'is_active' => true],
        ];

        foreach ($rows as $row) {
            DB::table('books')->updateOrInsert(
                ['isbn' => $row['isbn']],
                array_merge($row, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }
}
