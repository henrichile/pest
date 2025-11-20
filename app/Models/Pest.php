<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'scientific_name',
        'category',
        'description',
        'risks',
        'treatment',
        'technical_notes',
        'control_methods',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'control_methods' => 'array',
            'risks' => 'array',
            'treatment' => 'array',
            'is_active' => 'boolean',
        ];
    }


    /**
     * Get the treatments for this pest.
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Scope to get only active pests.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get pests by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
