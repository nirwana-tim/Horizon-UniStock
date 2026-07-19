<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FacultySeeder::class,
            ProgramLevelSeeder::class,
            ItemCategorySeeder::class,
            ItemTypeSeeder::class,
            ItemDepartmentSeeder::class,
            ItemSizeSeeder::class,
            VendorSeeder::class,
            StudyProgramSeeder::class,
            ItemSeeder::class,
            ItemVariantSeeder::class,
            ItemPriceSeeder::class,
        ]);
    }
}
