<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FacultySeeder::class,
            ItemCategorySeeder::class,
            ItemTypeSeeder::class,
            ItemDepartmentSeeder::class,
            ItemSizeSeeder::class,
            VendorSeeder::class,
            StudyProgramSeeder::class,
        ]);
    }
}
