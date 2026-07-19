<?php

namespace Database\Seeders\Master;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $category = ItemCategory::where('code', 'UNF')->first();
        $type = ItemType::where('code', 'SCB')->first();
        $department = ItemDepartment::where('code', '02')->first();

        if (!$category || !$type || !$department) {
            $this->command->warn('Master data not found. Run ItemCategory, ItemType, ItemDepartment seeders first.');

            return;
        }

        Item::firstOrCreate(
            ['code' => 'UNF-L-SCB-02-03'],
            [
                'name' => 'Uniform Scrub Laki - Laki STIKES, Size S',
                'base_code' => 'UNF-L-SCB-02',
                'gender' => 'L',
                'category_id' => $category->id,
                'type_id' => $type->id,
                'department_id' => $department->id,
                'unit' => 'pcs',
                'selling_price' => 180000,
                'hpp' => 0,
            ]
        );
    }
}
