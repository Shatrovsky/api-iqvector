<?php

namespace App\Traits;

use App\Models\Common\Address;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Используется теми Eloquent моделями, у которых есть адреса
 */
trait HasAddress
{
    /**
     * @return MorphOne
     */
    public function address()
    {
        return $this->morphOne(Address::class, 'essence');
    }
}
