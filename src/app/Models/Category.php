<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'level',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'position' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
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

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public static function descendantIdsAndSelf(int $categoryId): array
    {
        $rows = DB::select(
            '
            WITH RECURSIVE tree AS (
                SELECT id
                FROM categories
                WHERE id = ?

                UNION ALL

                SELECT c.id
                FROM categories c
                INNER JOIN tree t ON c.parent_id = t.id
            )
            SELECT id FROM tree
            ',
            [$categoryId]
        );

        return array_map(fn ($r) => (int) $r->id, $rows);
    }
}
