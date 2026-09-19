<?php

namespace Database\Seeders;

use App\Models\PrPackage;
use Illuminate\Database\Seeder;

class PrPackagesSeeder extends Seeder
{
    /**
     * Seed the six PR packages currently hardcoded on /pr-services, each with a
     * dummy "was" price (struck through) next to the real price. Idempotent —
     * keyed on name, so re-running won't duplicate or overwrite admin edits' ids.
     */
    public function run(): void
    {
        $packages = [
            [
                'label' => 'Starter', 'name' => 'Trial Pack',
                'original_price' => '1,999', 'price' => '999',
                'sub' => 'First-timer? Try us with 200+ outlets.',
                'features' => ['200+ Digital Outlets', 'Attention India · Daily Hunt', 'News18 Nation · Loktej', '24-hour delivery'],
                'badge' => null, 'is_popular' => false, 'sort_order' => 1,
            ],
            [
                'label' => 'Standard', 'name' => 'Basic Plus 250',
                'original_price' => '8,000', 'price' => '5,000',
                'sub' => 'Wider reach with premium regional press.',
                'features' => ['250+ Outlets', 'Lokmat Times · UNI', 'Ahmedabad Mirror · First India', 'Live coverage report'],
                'badge' => 'Most Popular', 'is_popular' => true, 'sort_order' => 2,
            ],
            [
                'label' => 'Premium', 'name' => 'NewsX Plus',
                'original_price' => '12,000', 'price' => '7,500',
                'sub' => 'High-authority national news network.',
                'features' => ['NewsX + India News', 'The Daily Guardian', 'HT Syndication · My Nation', '250+ supporting outlets'],
                'badge' => null, 'is_popular' => false, 'sort_order' => 3,
            ],
            [
                'label' => 'Premium', 'name' => 'ANI Plus',
                'original_price' => '12,500', 'price' => '7,800',
                'sub' => 'Wire-syndicated coverage via ANI.',
                'features' => ['ANI Wire syndication', '100+ verified placements', 'Tier-1 digital coverage', 'Live tracking dashboard'],
                'badge' => null, 'is_popular' => false, 'sort_order' => 4,
            ],
            [
                'label' => 'Editorial', 'name' => 'Midday Standard',
                'original_price' => '16,000', 'price' => '10,500',
                'sub' => 'Editorial placement on Mid-Day + regional.',
                'features' => ['Mid-Day editorial', 'Lokmat Times', '250+ digital outlets', 'Premium narrative drafting'],
                'badge' => null, 'is_popular' => false, 'sort_order' => 5,
            ],
            [
                'label' => 'Elite', 'name' => 'ANI + BS + PTI',
                'original_price' => '24,999', 'price' => '16,500',
                'sub' => 'Triple wire combo — maximum authority.',
                'features' => ['ANI Wire', 'Business Standard', 'PTI Wire', '400+ total placements'],
                'badge' => null, 'is_popular' => false, 'sort_order' => 6,
            ],
        ];

        foreach ($packages as $p) {
            $p['features'] = json_encode($p['features'], JSON_UNESCAPED_UNICODE);
            PrPackage::updateOrCreate(['name' => $p['name']], $p);
        }
    }
}
