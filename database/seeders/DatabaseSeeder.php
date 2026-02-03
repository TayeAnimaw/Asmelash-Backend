<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Project;
use App\Models\Item;
use App\Models\StockBalance;
use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StoreIssueVoucher;
use App\Models\StoreIssueVoucherItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create specific admin user
        $admin = User::create([
            'name' => 'Taye Animaw',
            'email' => 'tayeanimaw7@gmail.com',
            'phone' => '0912345678',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create additional users
        $users = User::factory()->count(5)->create();

        // Create Projects
        $projects = Project::factory()->count(10)->create();

        // Create Items
        $items = Item::factory()->count(20)->create();

        // Create Stock Balances for each project and item combination
        foreach ($projects as $project) {
            foreach ($items->random(5) as $item) {
                StockBalance::create([
                    'project_id' => $project->id,
                    'item_id' => $item->id,
                    'quantity' => rand(10, 500),
                    'unit' => $item->unit,
                    'unit_price' => rand(50, 5000),
                ]);
            }
        }

        // Create GRNs (Good Receiving Notes)
        $grns = Grn::factory()->count(15)->create();

        // Create GRN Items
        foreach ($grns as $grn) {
            foreach ($items->random(3) as $item) {
                GrnItem::create([
                    'grn_id' => $grn->id,
                    'item_id' => $item->id,
                    'quantity' => rand(5, 100),
                    'unit_price' => rand(50, 5000),
                    'received_quantity' => rand(0, 100),
                    'batch_number' => 'BATCH-' . rand(1000, 9999),
                    'expiry_date' => now()->addMonths(rand(1, 12)),
                    'notes' => fake()->sentence(),
                ]);
            }
        }

        // Create Stock Adjustments
        $stockAdjustments = StockAdjustment::factory()->count(10)->create();

        // Create Stock Adjustment Items
        foreach ($stockAdjustments as $adjustment) {
            foreach ($items->random(2) as $item) {
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'item_id' => $item->id,
                    'adjustment_type' => fake()->randomElement(['add', 'subtract', 'set']),
                    'quantity' => rand(1, 50),
                    'reason' => fake()->sentence(),
                ]);
            }
        }

        // Create Store Issue Vouchers
        $storeIssueVouchers = StoreIssueVoucher::factory()->count(10)->create();

        // Create Store Issue Voucher Items
        foreach ($storeIssueVouchers as $voucher) {
            foreach ($items->random(2) as $item) {
                StoreIssueVoucherItem::create([
                    'store_issue_voucher_id' => $voucher->id,
                    'item_id' => $item->id,
                    'quantity' => rand(1, 20),
                    'unit' => $item->unit,
                    'purpose' => fake()->sentence(),
                    'issued_to' => fake()->name(),
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin User: tayeanimaw7@gmail.com / 12345678');
    }
}
