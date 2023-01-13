<?php

namespace App\Traits;

use App\Models\Common\Contact;
use App\Models\Common\Person;

/**
 * Используется теми Eloquent моделями, с которыми связана сущность персоны
 */
trait HasPersons
{
    public function persons()
    {
        return $this->morphMany(Person::class, 'essence');
    }
}
