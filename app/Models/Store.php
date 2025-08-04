<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MenuItem;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'google_map',
        'phone',
        'image',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
