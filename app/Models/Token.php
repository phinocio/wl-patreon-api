<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $access
 * @property string $refresh
 * @property string $expires_in
 * @property string $expires
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereExpiresIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereRefresh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Token extends Model
{
    protected $fillable = [
        'access',
        'refresh',
        'expires_in',
        'expires',
    ];
}
