<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\ItemDepartment;
use App\Models\ItemVariant;
use App\Models\ItemSize;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    private array $categories = [];
    private array $types = [];
    private array $departments = [];
    private array $sizes = [];

    public function run(): void
    {
        $this->categories = [
            'KIT' => ItemCategory::where('code', 'KIT')->first(),
            'KTM' => ItemCategory::where('code', 'KTM')->first(),
            'UNF' => ItemCategory::where('code', 'UNF')->first(),
            'SHO' => ItemCategory::where('code', 'SHO')->first(),
            'MRC' => ItemCategory::where('code', 'MRC')->first(),
        ];

        $this->types = [
            'ALM' => ItemType::where('code', 'ALM')->first(),
            'CLG' => ItemType::where('code', 'CLG')->first(),
            'CLC' => ItemType::where('code', 'CLC')->first(),
            'SCB' => ItemType::where('code', 'SCB')->first(),
            'LAB' => ItemType::where('code', 'LAB')->first(),
            'COM' => ItemType::where('code', 'COM')->first(),
            'YDH' => ItemType::where('code', 'YDH')->first(),
            'KTM' => ItemType::where('code', 'KTM')->first(),
            'TAG' => ItemType::where('code', 'TAG')->first(),
            'NUR' => ItemType::where('code', 'NUR')->first(),
            'MID' => ItemType::where('code', 'MID')->first(),
            'TBR' => ItemType::where('code', 'TBR')->first(),
        ];

        $this->departments = [
            '01' => ItemDepartment::where('code', '01')->first(),
            '02' => ItemDepartment::where('code', '02')->first(),
            '03' => ItemDepartment::where('code', '03')->first(),
            '04' => ItemDepartment::where('code', '04')->first(),
            '05' => ItemDepartment::where('code', '05')->first(),
            '06' => ItemDepartment::where('code', '06')->first(),
            '07' => ItemDepartment::where('code', '07')->first(),
            '09' => ItemDepartment::where('code', '09')->first(),
            '14' => ItemDepartment::where('code', '14')->first(),
        ];

        $this->sizes = [
            '01' => ItemSize::where('code', '01')->first(),
            '02' => ItemSize::where('code', '02')->first(),
            '03' => ItemSize::where('code', '03')->first(),
            '04' => ItemSize::where('code', '04')->first(),
            '05' => ItemSize::where('code', '05')->first(),
            '06' => ItemSize::where('code', '06')->first(),
            '07' => ItemSize::where('code', '07')->first(),
            '34' => ItemSize::where('code', '34')->first(),
            '35' => ItemSize::where('code', '35')->first(),
            '36' => ItemSize::where('code', '36')->first(),
            '37' => ItemSize::where('code', '37')->first(),
            '38' => ItemSize::where('code', '38')->first(),
            '39' => ItemSize::where('code', '39')->first(),
            '40' => ItemSize::where('code', '40')->first(),
            '41' => ItemSize::where('code', '41')->first(),
            '42' => ItemSize::where('code', '42')->first(),
            'TM' => ItemSize::where('code', 'TM')->first(),
        ];

        // Kits
        $this->createItem('KIT-U-MID-02-01', 'Kit Midwifery Unisex STIKES', 'KIT', 'MID', 'U', '02', '01');
        $this->createItem('KIT-U-NUR-02-01', 'Kit Nursing Unisex STIKES', 'KIT', 'NUR', 'U', '02', '01');
        $this->createItem('KIT-U-NUR-05-01', 'Kit Nursing Unisex S1 KEP', 'KIT', 'NUR', 'U', '05', '01');
        $this->createItem('KIT-U-NUR-06-01', 'Kit Nursing Unisex D3 KEP', 'KIT', 'NUR', 'U', '06', '01');
        $this->createItem('KIT-U-NUR-09-01', 'Kit Nursing Unisex NERS', 'KIT', 'NUR', 'U', '09', '01');

        // KTM
        $this->createItem('KTM-U-KTM-01-01', 'KTM Kartu Mahasiswa Unisex Horizon', 'KTM', 'KTM', 'U', '01', '01');
        $this->createItem('KTM-U-TAG-02-01', 'KTM Tag Unisex STIKES', 'KTM', 'TAG', 'U', '02', '01');
        $this->createItem('KTM-U-YDH-01-01', 'KTM Lanyard & Holder Unisex Horizon', 'KTM', 'YDH', 'U', '01', '01');

        // Merchandise
        $this->createItem('MRC-U-TBR-01-TM', 'Merchandise Tumbler Unisex Horizon', 'MRC', 'TBR', 'U', '01', 'TM');

        // Shoes Laki-laki
        $this->createItem('SHO-L-CLC-02-37', 'Shoes Clinical Laki-Laki STIKES', 'SHO', 'CLC', 'L', '02', '37');
        $this->createItem('SHO-L-CLC-14-39', 'Shoes Clinical Laki-Laki S1 Pariwisata', 'SHO', 'CLC', 'L', '14', '39');
        $this->createItem('SHO-L-CLG-02-37', 'Shoes College Laki-Laki STIKES', 'SHO', 'CLG', 'L', '02', '37');

        // Shoes Perempuan
        $this->createItem('SHO-P-CLC-02-34', 'Shoes Clinical Perempuan STIKES', 'SHO', 'CLC', 'P', '02', '34');
        $this->createItem('SHO-P-CLC-14-36', 'Shoes Clinical Perempuan S1 Pariwisata', 'SHO', 'CLC', 'P', '14', '36');
        $this->createItem('SHO-P-CLG-02-34', 'Shoes College Perempuan STIKES', 'SHO', 'CLG', 'P', '02', '34');

        // Shoes Unisex Scrub
        $this->createItem('SHO-U-SCB-02-34', 'Shoes Scrub Unisex STIKES', 'SHO', 'SCB', 'U', '02', '34');

        // Uniforms Laki-laki
        $this->createItem('UNF-L-CLC-02-03', 'Uniform Clinical Laki-Laki STIKES', 'UNF', 'CLC', 'L', '02', '03');
        $this->createItem('UNF-L-CLC-03-03', 'Uniform Clinical Laki-Laki STMIK', 'UNF', 'CLC', 'L', '03', '03');
        $this->createItem('UNF-L-CLC-14-03', 'Uniform Clinical Laki-Laki S1 Pariwisata', 'UNF', 'CLC', 'L', '14', '03');
        $this->createItem('UNF-L-CLG-01-03', 'Uniform College Laki-Laki Horizon', 'UNF', 'CLG', 'L', '01', '03');
        $this->createItem('UNF-L-CLG-02-03', 'Uniform College Laki-Laki STIKES', 'UNF', 'CLG', 'L', '02', '03');
        $this->createItem('UNF-L-CLG-03-03', 'Uniform College Laki-Laki STMIK', 'UNF', 'CLG', 'L', '03', '03');
        $this->createItem('UNF-L-CLG-04-03', 'Uniform College Laki-Laki STIE', 'UNF', 'CLG', 'L', '04', '03');
        $this->createItem('UNF-L-COM-05-03', 'Uniform Community Laki-Laki S1 KEP', 'UNF', 'COM', 'L', '05', '03');
        $this->createItem('UNF-L-COM-06-03', 'Uniform Community Laki-Laki D3 KEP', 'UNF', 'COM', 'L', '06', '03');
        $this->createItem('UNF-L-SCB-02-03', 'Uniform Scrub Laki-Laki STIKES', 'UNF', 'SCB', 'L', '02', '03');

        // Uniforms Perempuan
        $this->createItem('UNF-P-CLC-01-05', 'Uniform Clinical Perempuan Horizon', 'UNF', 'CLC', 'P', '01', '05');
        $this->createItem('UNF-P-CLC-02-03', 'Uniform Clinical Perempuan STIKES', 'UNF', 'CLC', 'P', '02', '03');
        $this->createItem('UNF-P-CLC-03-03', 'Uniform Clinical Perempuan STMIK', 'UNF', 'CLC', 'P', '03', '03');
        $this->createItem('UNF-P-CLC-14-03', 'Uniform Clinical Perempuan S1 Pariwisata', 'UNF', 'CLC', 'P', '14', '03');
        $this->createItem('UNF-P-CLG-01-03', 'Uniform College Perempuan Horizon', 'UNF', 'CLG', 'P', '01', '03');
        $this->createItem('UNF-P-CLG-02-03', 'Uniform College Perempuan STIKES', 'UNF', 'CLG', 'P', '02', '03');
        $this->createItem('UNF-P-CLG-03-03', 'Uniform College Perempuan STMIK', 'UNF', 'CLG', 'P', '03', '03');
        $this->createItem('UNF-P-CLG-04-03', 'Uniform College Perempuan STIE', 'UNF', 'CLG', 'P', '04', '03');
        $this->createItem('UNF-P-COM-05-03', 'Uniform Community Perempuan S1 KEP', 'UNF', 'COM', 'P', '05', '03');
        $this->createItem('UNF-P-COM-06-03', 'Uniform Community Perempuan D3 KEP', 'UNF', 'COM', 'P', '06', '03');
        $this->createItem('UNF-P-COM-07-03', 'Uniform Community Perempuan D3 KEB', 'UNF', 'COM', 'P', '07', '03');
        $this->createItem('UNF-P-SCB-02-03', 'Uniform Scrub Perempuan STIKES', 'UNF', 'SCB', 'P', '02', '03');

        // Uniforms Unisex
        $this->createItem('UNF-U-ALM-01-03', 'Uniform Almamater Unisex Horizon', 'UNF', 'ALM', 'U', '01', '03');
        $this->createItem('UNF-U-ALM-01-04', 'Uniform Almamater Unisex Horizon', 'UNF', 'ALM', 'U', '01', '04');
        $this->createItem('UNF-U-ALM-01-05', 'Uniform Almamater Unisex Horizon', 'UNF', 'ALM', 'U', '01', '05');
        $this->createItem('UNF-U-ALM-01-06', 'Uniform Almamater Unisex Horizon', 'UNF', 'ALM', 'U', '01', '06');
        $this->createItem('UNF-U-ALM-01-07', 'Uniform Almamater Unisex Horizon', 'UNF', 'ALM', 'U', '01', '07');
        $this->createItem('UNF-U-ALM-02-03', 'Uniform Almamater Unisex STIKES', 'UNF', 'ALM', 'U', '02', '03');
        $this->createItem('UNF-U-ALM-02-04', 'Uniform Almamater Unisex STIKES', 'UNF', 'ALM', 'U', '02', '04');
        $this->createItem('UNF-U-ALM-02-05', 'Uniform Almamater Unisex STIKES', 'UNF', 'ALM', 'U', '02', '05');
        $this->createItem('UNF-U-ALM-02-06', 'Uniform Almamater Unisex STIKES', 'UNF', 'ALM', 'U', '02', '06');
        $this->createItem('UNF-U-ALM-02-07', 'Uniform Almamater Unisex STIKES', 'UNF', 'ALM', 'U', '02', '07');
        $this->createItem('UNF-U-ALM-03-03', 'Uniform Almamater Unisex STMIK', 'UNF', 'ALM', 'U', '03', '03');
        $this->createItem('UNF-U-ALM-04-03', 'Uniform Almamater Unisex STIE', 'UNF', 'ALM', 'U', '04', '03');
        $this->createItem('UNF-U-LAB-02-03', 'Uniform Laboratory Unisex STIKES', 'UNF', 'LAB', 'U', '02', '03');

        // Additional Uniform sizes (M, L, XL for key products)
        $this->createItem('UNF-L-SCB-02-04', 'Uniform Scrub Laki-Laki STIKES', 'UNF', 'SCB', 'L', '02', '04');
        $this->createItem('UNF-L-SCB-02-05', 'Uniform Scrub Laki-Laki STIKES', 'UNF', 'SCB', 'L', '02', '05');
        $this->createItem('UNF-L-SCB-02-06', 'Uniform Scrub Laki-Laki STIKES', 'UNF', 'SCB', 'L', '02', '06');
        $this->createItem('UNF-P-SCB-02-04', 'Uniform Scrub Perempuan STIKES', 'UNF', 'SCB', 'P', '02', '04');
        $this->createItem('UNF-P-SCB-02-05', 'Uniform Scrub Perempuan STIKES', 'UNF', 'SCB', 'P', '02', '05');
        $this->createItem('UNF-P-SCB-02-06', 'Uniform Scrub Perempuan STIKES', 'UNF', 'SCB', 'P', '02', '06');
        $this->createItem('UNF-L-CLC-02-04', 'Uniform Clinical Laki-Laki STIKES', 'UNF', 'CLC', 'L', '02', '04');
        $this->createItem('UNF-L-CLC-02-05', 'Uniform Clinical Laki-Laki STIKES', 'UNF', 'CLC', 'L', '02', '05');
        $this->createItem('UNF-L-CLC-02-06', 'Uniform Clinical Laki-Laki STIKES', 'UNF', 'CLC', 'L', '02', '06');
        $this->createItem('UNF-P-CLC-02-04', 'Uniform Clinical Perempuan STIKES', 'UNF', 'CLC', 'P', '02', '04');
        $this->createItem('UNF-P-CLC-02-05', 'Uniform Clinical Perempuan STIKES', 'UNF', 'CLC', 'P', '02', '05');
        $this->createItem('UNF-P-CLC-02-06', 'Uniform Clinical Perempuan STIKES', 'UNF', 'CLC', 'P', '02', '06');
        $this->createItem('UNF-L-CLG-02-04', 'Uniform College Laki-Laki STIKES', 'UNF', 'CLG', 'L', '02', '04');
        $this->createItem('UNF-L-CLG-02-05', 'Uniform College Laki-Laki STIKES', 'UNF', 'CLG', 'L', '02', '05');
        $this->createItem('UNF-L-CLG-02-06', 'Uniform College Laki-Laki STIKES', 'UNF', 'CLG', 'L', '02', '06');
        $this->createItem('UNF-P-CLG-02-04', 'Uniform College Perempuan STIKES', 'UNF', 'CLG', 'P', '02', '04');
        $this->createItem('UNF-P-CLG-02-05', 'Uniform College Perempuan STIKES', 'UNF', 'CLG', 'P', '02', '05');
        $this->createItem('UNF-P-CLG-02-06', 'Uniform College Perempuan STIKES', 'UNF', 'CLG', 'P', '02', '06');

        // Additional Shoe sizes
        $this->createItem('SHO-L-CLC-02-38', 'Shoes Clinical Laki-Laki STIKES', 'SHO', 'CLC', 'L', '02', '38');
        $this->createItem('SHO-L-CLC-02-39', 'Shoes Clinical Laki-Laki STIKES', 'SHO', 'CLC', 'L', '02', '39');
        $this->createItem('SHO-L-CLC-02-40', 'Shoes Clinical Laki-Laki STIKES', 'SHO', 'CLC', 'L', '02', '40');
        $this->createItem('SHO-L-CLC-02-41', 'Shoes Clinical Laki-Laki STIKES', 'SHO', 'CLC', 'L', '02', '41');
        $this->createItem('SHO-L-CLC-02-42', 'Shoes Clinical Laki-Laki STIKES', 'SHO', 'CLC', 'L', '02', '42');
        $this->createItem('SHO-P-CLC-02-35', 'Shoes Clinical Perempuan STIKES', 'SHO', 'CLC', 'P', '02', '35');
        $this->createItem('SHO-P-CLC-02-36', 'Shoes Clinical Perempuan STIKES', 'SHO', 'CLC', 'P', '02', '36');
        $this->createItem('SHO-P-CLC-02-37', 'Shoes Clinical Perempuan STIKES', 'SHO', 'CLC', 'P', '02', '37');
        $this->createItem('SHO-P-CLC-02-38', 'Shoes Clinical Perempuan STIKES', 'SHO', 'CLC', 'P', '02', '38');
        $this->createItem('SHO-P-CLC-02-39', 'Shoes Clinical Perempuan STIKES', 'SHO', 'CLC', 'P', '02', '39');
    }

    private function createItem(string $code, string $name, string $catCode, string $typeCode, string $gender, string $deptCode, string $sizeCode): void
    {
        $category = $this->categories[$catCode];
        $type = $this->types[$typeCode];
        $department = $this->departments[$deptCode];
        $size = $this->sizes[$sizeCode];

        if (!$category || !$type || !$department || !$size) {
            return;
        }

        // Calculate base_code by stripping the last size suffix (last 3 chars)
        // e.g., UNF-L-SCB-02-03 → UNF-L-SCB-02
        $baseCode = substr($code, 0, -3);

        $item = Item::firstOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'base_code' => $baseCode,
                'gender' => $gender,
                'category_id' => $category->id,
                'type_id' => $type->id,
                'department_id' => $department->id,
                'unit' => $catCode === 'KIT' ? 'set' : 'pcs',
                'selling_price' => 0,
                'hpp' => 0,
            ]
        );

        ItemVariant::firstOrCreate(
            ['sku' => $code],
            [
                'item_id' => $item->id,
                'size_id' => $size->id,
                'size' => $size->code,
                'size_label' => $size->label,
            ]
        );
    }
}
