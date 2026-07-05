<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@horizon-unistock.test'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            RolePermissionSeeder::class,
            FacultySeeder::class,
            StudyProgramSeeder::class,
            ProgramLevelSeeder::class,
            UserTestSeeder::class,
            ItemCategorySeeder::class,
            ItemTypeSeeder::class,
            ItemDepartmentSeeder::class,
            ItemSizeSeeder::class,
            CategoryItemSizeSeeder::class,
            CategoryItemTypeSeeder::class,
            VendorSeeder::class,
            ItemSeeder::class,
            ItemPriceSeeder::class,
            EntitlementSeeder::class,
        ]);
    }
}
