<?php

namespace App\Models\Traits;

use Ramsey\Uuid\Uuid;

/**
 * Trait Uuids
 *
 * @package Modules\Core\Traits
 */
trait Uuids
{
    /**
     * Boot function from laravel.
     */
    public static function bootUuids()
    {
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }
}
