<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'price',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
