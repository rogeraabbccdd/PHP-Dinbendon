<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'term' => 'integer',
        ];
    }

    /**
     * The users that belong to the course.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the group orders for the course.
     */
    public function groupOrders(): HasMany
    {
        return $this->hasMany(GroupOrder::class);
    }

    /**
     * Get the Taiwan year.
     */
    protected function year(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value - 1911
        );
    }
}
