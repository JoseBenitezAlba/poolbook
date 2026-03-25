<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Cita
 *
 * @property $id
 * @property $title
 * @property $start
 * @property $end
 * @property $resource_id
 * @property $user_id
 * @property $day_of_week
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cita extends Model
{
    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'start', 'end', 'resource_id', 'user_id', 'day_of_week', 'date'];
       /**
     * Get the user that owns the cita.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
