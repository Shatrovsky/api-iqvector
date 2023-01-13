<?php

namespace App\Models\Clubs;

use App\Traits\EditableEntity;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityGroup
 * @package App\Models\Clubs
 *
 * @property string $name
 *
 * @property Activity[] $activities
 */
class ActivityGroup extends Model
{
    use EditableEntity;
    protected $table = 'clubs.activity_groups';
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'activity_group_id');
    }
}
