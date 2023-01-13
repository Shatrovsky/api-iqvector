<?php

namespace App\Models\Clubs;

use App\Traits\CheckDelete;
use App\Traits\EditableEntity;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Activity
 * @package App\Models\Clubs
 *
 * @property string $name
 *
 * @property ActivityGroup $activityGroup
 * @property Club[] $clubs
 */
class Activity extends Model
{
    use EditableEntity;
    use CheckDelete;

    protected $table = 'clubs.activities';
    protected $fillable = ['name', 'activity_group_id'];
    public $relatedEntities = ['clubs'];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'clubs.clubs_activities', 'activity_id', 'club_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activityGroup()
    {
        return $this->belongsTo(ActivityGroup::class, 'activity_group_id');
    }
}
