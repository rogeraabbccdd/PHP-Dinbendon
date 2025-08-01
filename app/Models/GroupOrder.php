<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'store_id',
        'user_id',
        'status',
        'menu_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'menu_snapshot' => 'array',
        ];
    }
}
