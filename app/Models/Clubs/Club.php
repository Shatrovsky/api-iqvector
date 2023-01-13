<?php

namespace App\Models\Clubs;

use App\Helpers\File;
use App\Models\Common\FillableFromForm;
use App\Models\Common\UserProfileFavorite;
use App\Traits\CheckDelete;
use App\Traits\HasFiles;
use App\Traits\EditableEntity;
use App\Traits\HasNote;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Club
 * @package App\Models\Clubs
 *
 * @property integer $organization_id
 * @property string $type
 * @property string $name
 * @property string $short_name
 * @property string $description
 * @property string $achievements
 * @property array $photos
 * @property string $comment
 *
 * @property Organization $organization
 * @property Group[] $groups
 * @property Activity[] $activities
 */
class Club extends Model implements FillableFromForm
{
    use CheckDelete;
    use HasFiles;
    use EditableEntity;
    use HasNote;

    protected $casts = [
        'photos' => 'array'
    ];
    public $fieldsContainingFile = ['photos'];

    public $relatedEntities = ['groups'];

    const TYPE_SECTION = 'section';
    const TYPE_CLUB = 'club';
    const TYPE_STUDIO = 'studio';
    const TYPE_HOBBY = 'hobby';
    const TYPE_FITNESS = 'fitness';

    public static $types = [
        self::TYPE_SECTION => 'Секция',
        self::TYPE_CLUB => 'Клуб',
        self::TYPE_STUDIO => 'Студия',
        self::TYPE_HOBBY => 'Кружок',
        self::TYPE_FITNESS => 'Фитнес'
    ];

    protected $table = 'clubs.clubs';

    protected $fillable = [
        'organization_id',
        'type',
        'name',
        'short_name',
        'description',
        'achievements',
        'photos',
        'comment',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups()
    {
        return $this->hasMany(Group::class, 'club_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'clubs.clubs_activities', 'club_id', 'activity_id');
    }

    /**
     * @return mixed
     */
    public function favorite()
    {
        return $this->morphOne(UserProfileFavorite::class, 'essence');
    }

    public function getPhotosAttribute($values)
    {
        $photos = [];
        if (!empty($values)) {
            $values = json_decode($values);
            foreach ($values as $value) {
                $path = File::getPath($this, $value);
                $path .= $value;
                $photos[] = $path;
            }
        }
        return $photos;
    }

    public function partner()
    {
        return $this->morphTo();
    }
}
