<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property array $patrons
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patron newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patron newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patron query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patron whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patron whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patron wherePatrons($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patron whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Patron extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patrons',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'patrons' => 'array',
    ];
}
