<?php

namespace Database\Seeders\Master;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        Vendor::firstOrCreate(
            ['name' => 'PT Seragam Nusantara'],
            [
                'email' => 'sales@seragamnusantara.co.id',
                'contact' => 'Budi Santoso',
                'phone' => '081234567890',
            ]
        );
    }
}
