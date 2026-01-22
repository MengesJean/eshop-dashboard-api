<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'sku',
        'short_description',
        'active',
        'weight',
    ];

    /**
     * Attribute casting.
     */

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'price' => 'decimal:2',
        'sku' => 'string',
        'short_description' => 'string',
        'description' => 'string',
        'stock' => 'integer',
        'active' => 'boolean',
        'weight' => 'decimal:2',
    ];

    /**
     * normalize/format values before saving.
     * (Keeps decimals consistent even if floats slip in.)
     */
    protected function setPriceAttribute($value): void
    {
        $this->attributes['price'] = is_null($value) ? '0.00' : number_format((float)$value, 2, '.', '');
    }

    protected function setWeightAttribute($value): void
    {
        $this->attributes['weight'] = is_null($value) ? '0.00' : number_format((float)$value, 2, '.', '');
    }

    /**
     * Query scopes for common API filters.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeDeactive(Builder $query): Builder
    {
        return $query->where('active', false);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock', '<=', 0);
    }


    /**
     * Convenience helpers
     */
    public function activate(): void
    {
        $this->forceFill(['active' => true])->save();
    }

    public function deactivate(): void
    {
        $this->forceFill(['active' => false])->save();
    }
}
