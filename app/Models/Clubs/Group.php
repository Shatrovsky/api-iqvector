<?php

namespace App\Models\Clubs;

use App\Helpers\File;
use App\Models\Common\FillableFromForm;
use App\Traits\HasFiles;
use App\Traits\Clubs\HasContacts;
use App\Traits\EditableEntity;
use App\Traits\HasNote;
use App\Models\Locations\City;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 * @package App\Models\Clubs
 *
 * @property integer $club_id
 * @property integer $teacher_id
 * @property string $gender
 * @property string $name
 * @property integer $age_from
 * @property integer $age_to
 * @property boolean $paid
 * @property integer $cost_per_month
 * @property integer $cost_per_lesson
 * @property boolean $intro_lesson
 * @property boolean $exam
 * @property string $exam_description
 * @property string $description
 * @property integer $city_id
 * @property integer $postal_code
 * @property string $address
 * @property string $street
 * @property string $house
 * @property string $corps
 * @property string $building
 * @property string $letter
 * @property double $lat
 * @property double $lng
 * @property array $schedule
 * @property array $photos
 * @property string $comment
 *
 * @property Club $club
 * @property City $city
 * @property Teacher[] $teachers
 */
class Group extends Model implements FillableFromForm
{
    use HasFiles;
    use EditableEntity;
    use HasContacts;
    use HasNote;

    public $fieldsContainingFile = ['photos'];

    protected $table = 'clubs.groups';

    protected $casts = [
        'schedule' => 'array',
        'photos' => 'array'
    ];

    protected $fillable = [
        'club_id',
        'teacher_id',
        'name',
        'gender',
        'age_from',
        'age_to',
        'paid',
        'cost_per_month',
        'cost_per_lesson',
        'intro_lesson',
        'exam',
        'exam_description',
        'description',
        'city_id',
        'postal_code',
        'address',
        'street',
        'house',
        'corps',
        'building',
        'letter',
        'lat',
        'lng',
        'schedule',
        'comment',
        'photos'
    ];

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_ALL = 'all';
    public static $genderDictionary = [
        self::GENDER_ALL => 'Все',
        self::GENDER_MALE => 'Мальчики',
        self::GENDER_FEMALE => 'Девочки',
    ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (empty($model->city_id)) {
                $model->city_id = $model->club->organization->city_id;
            }
        });
    }

    public static function publicFields()
    {
        return [
            'id',
            'club_id',
            'name',
            'gender',
            'age_from',
            'age_to',
            'paid',
            'cost_per_month',
            'cost_per_lesson',
            'intro_lesson',
            'exam',
            'exam_description',
            'description',
            'city_id',
            'postal_code',
            'address',
            'street',
            'house',
            'corps',
            'building',
            'letter',
            'lat',
            'lng',
            'schedule',
            'photos'
        ];
    }

    /**
     * @return array
     */
    public function getOwnersIds()
    {
        return $this->club->getOwnersIds();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'clubs.groups_teachers', 'group_id', 'teacher_id');
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
}
