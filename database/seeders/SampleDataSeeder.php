<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DynamicTable;
use App\Models\TableColumn;
use App\Models\TableRow;
use App\Models\Language;
use App\Models\SiteSetting;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Set default site settings
        SiteSetting::set('site_name', 'SmartTable CMS');
        SiteSetting::set('meta_title', 'SmartTable CMS - Dynamic Data Management');
        SiteSetting::set('meta_description', 'A powerful dynamic table management system with multilingual support.');
        SiteSetting::set('currency_symbol', '₹');
        SiteSetting::set('currency_position', 'before');

        // Create sample "Products" table
        $table = DynamicTable::create([
            'name' => 'Products',
            'slug' => 'products',
            'description' => 'Sample product catalog with prices and descriptions.',
            'is_active' => true,
        ]);

        // Add columns
        $columns = [
            ['name' => 'Name', 'slug' => 'name', 'type' => 'text', 'is_translatable' => true, 'is_filterable' => true, 'order' => 1],
            ['name' => 'Price', 'slug' => 'price', 'type' => 'currency', 'is_translatable' => false, 'is_filterable' => true, 'order' => 2],
            ['name' => 'Remark', 'slug' => 'remark', 'type' => 'text', 'is_translatable' => true, 'is_filterable' => false, 'order' => 3],
            ['name' => 'Action', 'slug' => 'action', 'type' => 'button', 'is_translatable' => false, 'is_filterable' => false, 'order' => 4],
        ];

        foreach ($columns as $col) {
            $table->columns()->create($col);
        }

        // Get languages
        $english = Language::where('code', 'en')->first();
        $hindi = Language::where('code', 'hi')->first();

        // Sample products
        $products = [
            [
                'data' => ['price' => 999, 'action' => 'Buy Now|https://example.com/buy/1'],
                'en' => ['name' => 'Smartphone', 'remark' => 'Latest model with advanced features'],
                'hi' => ['name' => 'स्मार्टफोन', 'remark' => 'उन्नत सुविधाओं के साथ नवीनतम मॉडल'],
            ],
            [
                'data' => ['price' => 1499, 'action' => 'Buy Now|https://example.com/buy/2'],
                'en' => ['name' => 'Laptop', 'remark' => 'High performance computing'],
                'hi' => ['name' => 'लैपटॉप', 'remark' => 'उच्च प्रदर्शन कंप्यूटिंग'],
            ],
            [
                'data' => ['price' => 299, 'action' => 'Buy Now|https://example.com/buy/3'],
                'en' => ['name' => 'Headphones', 'remark' => 'Premium sound quality'],
                'hi' => ['name' => 'हेडफ़ोन', 'remark' => 'प्रीमियम ध्वनि गुणवत्ता'],
            ],
            [
                'data' => ['price' => 799, 'action' => 'Buy Now|https://example.com/buy/4'],
                'en' => ['name' => 'Tablet', 'remark' => 'Perfect for entertainment'],
                'hi' => ['name' => 'टैबलेट', 'remark' => 'मनोरंजन के लिए उपयुक्त'],
            ],
            [
                'data' => ['price' => 199, 'action' => 'Buy Now|https://example.com/buy/5'],
                'en' => ['name' => 'Smart Watch', 'remark' => 'Fitness tracking included'],
                'hi' => ['name' => 'स्मार्ट वॉच', 'remark' => 'फिटनेस ट्रैकिंग शामिल'],
            ],
            [
                'data' => ['price' => 599, 'action' => 'Buy Now|https://example.com/buy/6'],
                'en' => ['name' => 'Camera', 'remark' => 'Professional photography'],
                'hi' => ['name' => 'कैमरा', 'remark' => 'पेशेवर फोटोग्राफी'],
            ],
            [
                'data' => ['price' => 149, 'action' => 'Buy Now|https://example.com/buy/7'],
                'en' => ['name' => 'Keyboard', 'remark' => 'Mechanical switches'],
                'hi' => ['name' => 'कीबोर्ड', 'remark' => 'मैकेनिकल स्विच'],
            ],
            [
                'data' => ['price' => 89, 'action' => 'Buy Now|https://example.com/buy/8'],
                'en' => ['name' => 'Mouse', 'remark' => 'Wireless connectivity'],
                'hi' => ['name' => 'माउस', 'remark' => 'वायरलेस कनेक्टिविटी'],
            ],
            [
                'data' => ['price' => 399, 'action' => 'Buy Now|https://example.com/buy/9'],
                'en' => ['name' => 'Monitor', 'remark' => '4K Ultra HD display'],
                'hi' => ['name' => 'मॉनिटर', 'remark' => '4K अल्ट्रा HD डिस्प्ले'],
            ],
            [
                'data' => ['price' => 249, 'action' => 'Buy Now|https://example.com/buy/10'],
                'en' => ['name' => 'Speaker', 'remark' => 'Bluetooth enabled'],
                'hi' => ['name' => 'स्पीकर', 'remark' => 'ब्लूटूथ सक्षम'],
            ],
        ];

        $order = 1;
        foreach ($products as $product) {
            $row = $table->rows()->create([
                'data' => $product['data'],
                'order' => $order++,
            ]);

            // English translation
            if ($english) {
                $row->setTranslation($english->id, $product['en']);
            }

            // Hindi translation
            if ($hindi) {
                $row->setTranslation($hindi->id, $product['hi']);
            }
        }
    }
}
