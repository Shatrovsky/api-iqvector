<?php

namespace App\Models\Clubs;

use App\Helpers\File;
use App\Models\Common\FillableFromForm;
use App\Traits\HasFiles;
use App\Traits\EditableEntity;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Teacher
 * @package App\Models\Clubs
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $position
 * @property string $description
 * @property string $photo
 * @property string $achievements
 *
 * @property Club $club
 * @property Group[] $groups
 * @property Organization $organization
 */
class Teacher extends Model implements FillableFromForm
{
    use HasFiles;
    use EditableEntity;

    public $fieldsContainingFile = ['photo'];

    protected $table = 'clubs.teachers';

    protected $fillable = [
        'organization_id',
        'first_name',
        'last_name',
        'middle_name',
        'description',
        'photo',
        'position',
        'achievements'
    ];

    /**
     * @return array
     */
    public function getOwnersIds()
    {
        return $this->organization->getOwnersIds();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'clubs.groups_teachers', 'teacher_id', 'group_id');
    }

    public function getPhotoAttribute($value)
    {
        $path = File::getPath($this, $value);
        $path .= $value;
        return $path;
    }

    public function getFullnameAttribute()
    {
        return $this->last_name . " " . $this->first_name . " " . $this->middle_name;
    }
}
