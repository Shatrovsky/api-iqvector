<?php

namespace App\Traits;

use App\Models\Common\Note;

/**
 * Используется теми Eloquent моделями, для которых можно оставлять заметки
 */
trait HasNote
{
    /**
     * @return mixed
     */
    public function notes()
    {
        return $this->morphMany(Note::class, 'essence');
    }
}
