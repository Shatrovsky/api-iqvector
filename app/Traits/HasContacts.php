<?php

namespace App\Traits;

use App\Models\Common\Contact;

/**
 * Используется теми Eloquent моделями, с которыми связана сущность контакта
 */
trait HasContacts
{
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'essence');
    }
}
