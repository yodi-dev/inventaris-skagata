<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Peralatan Bengkel',
                'description' => 'Alat praktik mekanik untuk jurusan Otomotif dan Pemesinan.'
            ],
            [
                'name' => 'Elektronik & IT',
                'description' => 'Komputer, laptop, proyektor, kabel jaringan, dll.'
            ],
            [
                'name' => 'Perabot Ruangan',
                'description' => 'Meja, kursi, lemari besi, dan papan tulis.'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
