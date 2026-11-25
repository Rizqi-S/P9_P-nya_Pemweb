<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Product model representing school supply sales data.
 */
class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'produk',
        'harga',
        'keuntungan',
        'terjual',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'keuntungan' => 'integer',
            'terjual' => 'integer',
        ];
    }
}
