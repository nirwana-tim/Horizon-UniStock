<?php

namespace Database\Seeders;

use App\Models\DistributionItem;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\StudentGeneration;
use App\Models\StockBalance;
use App\Models\StockReceive;
use App\Models\StockReceiveItem;
use App\Models\Student;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now = Carbon::now();

        $itemIds = Item::pluck('id')->toArray();
        $variantIds = ItemVariant::pluck('id')->toArray();
        $studentIds = Student::pluck('id')->toArray();
        $staffIds = User::role(['super_admin', 'admin', 'staff'])->pluck('id')->toArray();
        $programLevelIds = StudentGeneration::pluck('id')->toArray();
        $itemPrices = Item::pluck('selling_price', 'id')->toArray();

        if (empty($itemIds) || empty($studentIds) || empty($staffIds)) {
            $this->command->error('Data master belum cukup. Butuh items, students, dan users.');
            return;
        }

        // ===== Phase 1: Distribution Schedules =====
        $scheduleDates = [
            '2026-05-15', '2026-05-20', '2026-05-25',
            '2026-06-01', '2026-06-08', '2026-06-15', '2026-06-22', '2026-06-29',
            '2026-07-03', '2026-07-06',
        ];

        $scheduleIds = [];
        foreach ($scheduleDates as $i => $date) {
            $scheduleIds[] = DistributionSchedule::firstOrCreate(
                ['name' => 'Jadwal ' . ($i + 1), 'date' => $date],
                [
                    'name' => 'Jadwal ' . ($i + 1),
                    'date' => $date,
                    'period' => '2025/2026',
                    'location' => $faker->randomElement(['Gedung Serbaguna A', 'Aula Fakultas Kedokteran', 'Gedung Rektorat Lantai 2', 'Fakultas Teknik']),
                    'session' => $faker->randomElement(['08:00-10:00', '10:00-12:00', '13:00-15:00', '15:00-17:00']),
                    'is_active' => true,
                    'generation_id' => $faker->randomElement($programLevelIds),
                ]
            )->id;
        }

        $this->command->info('Phase 1: ' . count($scheduleIds) . ' schedules created');

        // ===== Phase 2: Vendor + Stock Receives =====
        $vendorId = Vendor::firstOrCreate(
            ['name' => 'PT Seragam Nusantara'],
            [
                'email' => 'sales@seragamnusantara.co.id',
                'contact' => 'Budi Santoso',
                'phone' => '081234567890',
            ]
        )->id;

        $receiveDates = ['2026-05-10', '2026-06-05', '2026-06-28'];
        $receiveIds = [];

        foreach ($receiveDates as $rDate) {
            $receiveIds[] = StockReceive::firstOrCreate(
                ['reference_number' => 'PO/2026/' . str_pad(count($receiveIds) + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'reference_number' => 'PO/2026/' . str_pad(count($receiveIds) + 1, 4, '0', STR_PAD_LEFT),
                    'vendor_id' => $vendorId,
                    'receive_date' => $rDate,
                    'status' => 'received',
                    'notes' => 'Penerimaan ' . $rDate,
                ]
            )->id;
        }

        foreach ($receiveIds as $rId) {
            $batchItems = $faker->randomElements($itemIds, 40);
            foreach ($batchItems as $iId) {
                $qty = $faker->numberBetween(100, 500);
                $price = $itemPrices[$iId] ?? 50000;
                $itemVariantIds = ItemVariant::where('item_id', $iId)->pluck('id')->toArray();
                $selectedVariantId = !empty($itemVariantIds) ? $faker->randomElement($itemVariantIds) : null;

                if (!$selectedVariantId) continue;

                StockReceiveItem::firstOrCreate(
                    ['stock_receive_id' => $rId, 'item_id' => $iId, 'variant_id' => $selectedVariantId],
                    [
                        'stock_receive_id' => $rId,
                        'item_id' => $iId,
                        'variant_id' => $selectedVariantId,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'hpp' => (int) ($price * 0.7),
                    ]
                );
            }
        }

        $this->command->info('Phase 2: ' . count($receiveIds) . ' stock receives created');

        // ===== Phase 3: Stock Balances =====
        foreach ($itemIds as $iId) {
            $itemVariantIds = ItemVariant::where('item_id', $iId)->pluck('id')->toArray();
            $selectedVariantId = !empty($itemVariantIds) ? $faker->randomElement($itemVariantIds) : null;

            if (!$selectedVariantId) continue;

            StockBalance::firstOrCreate(
                ['item_id' => $iId, 'variant_id' => $selectedVariantId],
                [
                    'item_id' => $iId,
                    'variant_id' => $selectedVariantId,
                    'quantity' => $faker->numberBetween(50, 500),
                    'last_hpp' => ($itemPrices[$iId] ?? 50000) * 0.7,
                ]
            );
        }

        $this->command->info('Phase 3: ' . count($itemIds) . ' stock balances created');

        // ===== Phase 4: Distribution Transactions + Items =====
        $transactionCount = 0;
        $itemCount = 0;

        foreach ($scheduleIds as $sId) {
            $schedule = DistributionSchedule::find($sId);
            if (!$schedule) continue;

            $txPerSchedule = $faker->numberBetween(5, 12);

            for ($t = 0; $t < $txPerSchedule; $t++) {
                $pickupTime = Carbon::parse($schedule->date)
                    ->addHours($faker->numberBetween(8, 16))
                    ->addMinutes($faker->numberBetween(0, 59));

                $studentId = $faker->randomElement($studentIds);

                $tx = DistributionTransaction::firstOrCreate(
                    ['student_id' => $studentId, 'schedule_id' => $sId],
                    [
                        'student_id' => $studentId,
                        'schedule_id' => $sId,
                        'staff_id' => $faker->randomElement($staffIds),
                        'status' => $faker->randomElement(['completed', 'completed', 'completed', 'partial']),
                        'pickup_time' => $pickupTime,
                        'notes' => $faker->optional(0.3)->sentence(),
                    ]
                );

                $transactionCount++;

                $txItems = $faker->randomElements($itemIds, $faker->numberBetween(2, 5));
                foreach ($txItems as $iId) {
                    DistributionItem::firstOrCreate(
                        ['transaction_id' => $tx->id, 'item_id' => $iId],
                        [
                            'transaction_id' => $tx->id,
                            'item_id' => $iId,
                            'expected_size' => $faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
                            'actual_size' => $faker->randomElement(['S', 'M', 'L', 'XL', 'XXL', null]),
                            'quantity' => $faker->numberBetween(1, 3),
                            'hpp' => ($itemPrices[$iId] ?? 50000) * 0.7,
                            'selling_price_at_distribution' => $itemPrices[$iId] ?? 50000,
                        ]
                    );
                    $itemCount++;
                }
            }
        }

        $this->command->info('Phase 4: ' . $transactionCount . ' transactions with ' . $itemCount . ' items created');
        $this->command->info('');
        $this->command->info('=== SEEDING COMPLETE ===');
    }
}
