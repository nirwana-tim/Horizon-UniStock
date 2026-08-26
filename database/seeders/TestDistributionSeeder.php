<?php

namespace Database\Seeders;

use App\Models\DistributionSchedule;
use App\Models\EligibilityRecord;
use App\Models\Entitlement;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemDepartment;
use App\Models\ItemSize;
use App\Models\ItemType;
use App\Models\SizeChangeEvent;
use App\Models\Student;
use App\Models\StudentGeneration;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\Vendor;
use App\Services\DistributionScheduleService;
use App\Services\EntitlementService;
use App\Services\Master\ItemService;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDistributionSeeder extends Seeder
{
    private const STUDENT_NIM = '4112757201240008';

    private const STUDENT_PASSWORD = 'password';

    public function run(): void
    {
        $superadmin = User::where('email', 'superadmin@horizon-unistock.test')->first()
            ?? User::first();

        // ---- Master refs ----
        $unf = ItemCategory::where('code', 'UNF')->firstOrFail();
        $sho = ItemCategory::where('code', 'SHO')->firstOrFail();
        $typeAlm = ItemType::where('code', 'ALM')->first();
        $typeCom = ItemType::where('code', 'COM')->first();
        $dept = ItemDepartment::where('code', '10')->first();
        $faculty = Faculty::where('code', 'FICT')->firstOrFail();
        $program = StudyProgram::where('code', 'S1 SI')->firstOrFail();
        $vendor = Vendor::first() ?? Vendor::create([
            'name' => 'PT Seragam Nusantara',
            'email' => 'sales@seragamnusantara.co.id',
            'contact' => 'Budi Santoso',
            'phone' => '081234567890',
        ]);
        $generation = StudentGeneration::firstOrCreate(
            ['code' => '2526'],
            ['name' => '2025/2026']
        );

        // ---- Items (UNF + SHO) via real ItemService ----
        $itemService = app(ItemService::class);
        $unfItem = $this->createItem($itemService, $unf, $typeAlm, $dept, 'U', 250000, 175000);
        $shoItem = $this->createItem($itemService, $sho, $typeCom, $dept, 'U', 150000, 105000);

        // ---- Stock receive for M(04) & Sepatu(38) ----
        $unfVariant = $unfItem->variants()->where('size', '04')->firstOrFail();
        $shoVariant = $shoItem->variants()->where('size', '38')->firstOrFail();

        app(StockService::class)->receiveStock([
            'vendor_id' => $vendor->id,
            'receive_date' => today(),
            'notes' => 'Stok uji distribusi (UNF M / SHO 38)',
            'items' => [
                ['item_id' => $unfItem->id, 'variant_id' => $unfVariant->id, 'quantity' => 50, 'unit_price' => 250000, 'hpp' => 175000],
                ['item_id' => $shoItem->id, 'variant_id' => $shoVariant->id, 'quantity' => 50, 'unit_price' => 150000, 'hpp' => 105000],
            ],
        ]);

        // ---- Student + akun login ----
        $studentUser = User::firstOrCreate(
            ['email' => 'student.test@horizon-unistock.test'],
            [
                'name' => 'Mahasiswa Uji',
                'password' => Hash::make(self::STUDENT_PASSWORD),
                'must_change_password' => false,
                'is_active' => true,
            ]
        );
        if (! $studentUser->hasRole('student')) {
            $studentUser->assignRole('student');
        }

        $student = Student::firstOrCreate(
            ['nim' => self::STUDENT_NIM],
            [
                'user_id' => $studentUser->id,
                'name' => 'Mahasiswa Uji',
                'gender' => 'L',
                'email_kampus' => 'student.test@horizon-unistock.test',
                'email_pribadi' => null,
                'study_program_id' => $program->id,
                'generation_id' => $generation->id,
                'student_level' => 'Y1S1',
                'status' => 'active',
                'current_semester' => 'Y1S1',
            ]
        );
        $student->update(['user_id' => $studentUser->id]);

        // entitlement_code = Y1S1FICTS1 SI
        $entitlementCode = $student->student_level.$faculty->code.$program->code;
        $student->update(['entitlement_code' => $entitlementCode]);

        // ---- Eligibility (Lunas) ----
        EligibilityRecord::firstOrCreate(
            ['student_id' => $student->id],
            ['is_eligible' => true, 'payment_status' => 'Paid']
        );

        // ---- Entitlement via real service ----
        $existingEntitlement = Entitlement::where('code', $entitlementCode)->first();
        if (! $existingEntitlement) {
            app(EntitlementService::class)->createEntitlement([
                'code' => $entitlementCode,
                'student_level' => 'Y1S1',
                'description' => 'Entitlement uji distribusi Y1S1 FICT S1 SI',
                'is_active' => true,
                'items' => [
                    ['item_id' => $unfItem->id, 'quantity' => 1],
                    ['item_id' => $shoItem->id, 'quantity' => 1],
                ],
            ]);
        }

        // ---- Event pengisian ukuran (agar saveSizes bisa dipakai) ----
        SizeChangeEvent::firstOrCreate(
            ['title' => 'Pengisian Ukuran Uji Distribusi'],
            [
                'description' => 'Event uji distribusi',
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(7),
                'faculty_id' => $faculty->id,
                'study_program_id' => $program->id,
                'generation_id' => $generation->id,
                'student_level' => 'Y1S1',
                'max_changes' => 1,
                'is_active' => true,
                'allow_reedit' => false,
                'created_by' => $superadmin?->id,
                'baju_size_options' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'sepatu_size_options' => ['38', '39', '40', '41', '42', '43', '44'],
            ]
        );

        // ---- Jadwal distribusi HARI INI via real service ----
        $today = today();
        $existingSchedule = DistributionSchedule::where('name', 'Jadwal Uji Hari Ini')->whereDate('date', $today)->first();
        if (! $existingSchedule) {
            app(DistributionScheduleService::class)->store([
                'name' => 'Jadwal Uji Hari Ini',
                'period' => '2025/2026',
                'student_level' => 'Y1S1',
                'date' => $today,
                'location' => 'Gedung Serbaguna A',
                'session' => '08:00-10:00',
                'is_active' => true,
                'faculty_id' => $faculty->id,
                'study_program_id' => $program->id,
                'generation_id' => $generation->id,
                'item_ids' => [$unfItem->id, $shoItem->id],
            ]);
        }

        $this->command->info('=== TEST DATA SIAP ===');
        $this->command->info('Student NIM   : '.self::STUDENT_NIM);
        $this->command->info('Student login : student.test@horizon-unistock.test / '.self::STUDENT_PASSWORD);
        $this->command->info('Entitlement   : '.$entitlementCode);
        $this->command->info('Items         : '.$unfItem->code.' (UNF M=04) & '.$shoItem->code.' (SHO 38)');
    }

    private function createItem(
        ItemService $service,
        ItemCategory $category,
        ?ItemType $type,
        ?ItemDepartment $department,
        string $gender,
        int $sellingPrice,
        int $hpp
    ): Item {
        $sizeIds = ItemSize::whereHas('categories', fn ($q) => $q->where('item_category_id', $category->id))
            ->pluck('id')
            ->all();

        return $service->store([
            'category_id' => $category->id,
            'type_id' => $type?->id,
            'department_id' => $department?->id,
            'gender' => $gender,
            'size_ids' => $sizeIds,
            'unit' => 'pcs',
            'selling_price' => $sellingPrice,
            'hpp' => $hpp,
        ]);
    }
}
