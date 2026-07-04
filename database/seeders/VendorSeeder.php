<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'PT Seragam Nusantara',
                'email' => 'contact@seragam-nusantara.co.id',
                'contact' => 'Budi Santoso',
                'phone' => '081234567890',
            ],
            [
                'name' => 'CV Konveksi Abadi',
                'email' => 'order@konveksi-abadi.co.id',
                'contact' => 'Siti Rahayu',
                'phone' => '082345678901',
            ],
            [
                'name' => 'PT Sepatu Nusantara',
                'email' => 'info@sepatu-nusantara.co.id',
                'contact' => 'Andi Wijaya',
                'phone' => '083456789012',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(
                ['email' => $vendor['email']],
                $vendor
            );
        }
    }
}
