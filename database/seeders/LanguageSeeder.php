<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'code' => 'hi',
                'name' => 'Hindi',
                'native_name' => 'हिंदी',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
                'native_name' => 'Español',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
