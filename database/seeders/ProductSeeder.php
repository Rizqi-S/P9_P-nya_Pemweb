<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Seeds the 30 initial school supply products.
 */
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $dataAwal = [
            ['harga' => 5000, 'keuntungan' => 2000, 'produk' => 'Pulpen', 'terjual' => 65],
            ['harga' => 12000, 'keuntungan' => 5000, 'produk' => 'Buku Gambar', 'terjual' => 210],
            ['harga' => 2000, 'keuntungan' => 700, 'produk' => 'Penggaris', 'terjual' => 95],
            ['harga' => 30000, 'keuntungan' => 5000, 'produk' => 'Botol Minum', 'terjual' => 320],
            ['harga' => 16000, 'keuntungan' => 3000, 'produk' => 'Tempat Pensil', 'terjual' => 140],
            ['harga' => 2000, 'keuntungan' => 500, 'produk' => 'BET', 'terjual' => 48],
            ['harga' => 4000, 'keuntungan' => 1000, 'produk' => 'Pensil', 'terjual' => 260],
            ['harga' => 3000, 'keuntungan' => 1000, 'produk' => 'Penghapus', 'terjual' => 115],
            ['harga' => 150000, 'keuntungan' => 20000, 'produk' => 'Tas Ransel', 'terjual' => 175],
            ['harga' => 1000, 'keuntungan' => 200, 'produk' => 'Peruncing', 'terjual' => 35],
            ['harga' => 180000, 'keuntungan' => 25000, 'produk' => 'Sepatu', 'terjual' => 380],
            ['harga' => 6000, 'keuntungan' => 1000, 'produk' => 'Dasi', 'terjual' => 75],
            ['harga' => 10000, 'keuntungan' => 3000, 'produk' => 'Gesper', 'terjual' => 155],
            ['harga' => 7000, 'keuntungan' => 2000, 'produk' => 'Topi', 'terjual' => 240],
            ['harga' => 10000, 'keuntungan' => 2000, 'produk' => 'Kaos Kaki', 'terjual' => 58],
            ['harga' => 5000, 'keuntungan' => 1000, 'produk' => 'Buku Tulis', 'terjual' => 130],
            ['harga' => 100000, 'keuntungan' => 5000, 'produk' => 'Seragam', 'terjual' => 290],
            ['harga' => 5000, 'keuntungan' => 1000, 'produk' => 'Gunting', 'terjual' => 90],
            ['harga' => 15000, 'keuntungan' => 2000, 'produk' => 'Sticky Note', 'terjual' => 205],
            ['harga' => 8000, 'keuntungan' => 2000, 'produk' => 'Correction Tape', 'terjual' => 42],
            ['harga' => 2000, 'keuntungan' => 300, 'produk' => 'Lem', 'terjual' => 340],
            ['harga' => 10000, 'keuntungan' => 2000, 'produk' => 'Origami', 'terjual' => 105],
            ['harga' => 50000, 'keuntungan' => 17000, 'produk' => 'Krayon', 'terjual' => 185],
            ['harga' => 5000, 'keuntungan' => 1000, 'produk' => 'Stabilo', 'terjual' => 70],
            ['harga' => 10000, 'keuntungan' => 2000, 'produk' => 'Spidol', 'terjual' => 275],
            ['harga' => 20000, 'keuntungan' => 5000, 'produk' => 'Pensil Warna', 'terjual' => 160],
            ['harga' => 20000, 'keuntungan' => 3000, 'produk' => 'Kacu Pramuka', 'terjual' => 30],
            ['harga' => 10000, 'keuntungan' => 1500, 'produk' => 'Stapler', 'terjual' => 230],
            ['harga' => 50000, 'keuntungan' => 4000, 'produk' => 'Meja Belajar', 'terjual' => 100],
            ['harga' => 10000, 'keuntungan' => 1500, 'produk' => 'Lakban', 'terjual' => 400],
        ];

        $records = array_map(fn (array $item): array => [
            'produk' => $item['produk'],
            'harga' => $item['harga'],
            'keuntungan' => $item['keuntungan'],
            'terjual' => $item['terjual'],
            'created_at' => $now,
            'updated_at' => $now,
        ], $dataAwal);

        Product::insert($records);
    }
}
