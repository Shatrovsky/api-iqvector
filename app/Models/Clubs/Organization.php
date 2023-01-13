<?php

namespace App\Models\Clubs;

use App\Helpers\File;
use App\Models\Common\FillableFromForm;
use App\Models\Locations\City;
use App\Providers\AppServiceProvider;
use App\Traits\CheckDelete;
use App\Traits\Clubs\HasContacts;
use App\Traits\HasFiles;
use App\Traits\EditableEntity;
use App\Traits\HasNote;
use App\Traits\HasPartners;
use App\Traits\Ownerable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Organization
 * @package App\Models\Clubs
 *
 * @property integer $city_id
 * @property string $ownership
 * @property integer $postal_code
 * @property string $address
 * @property string $street
 * @property string $house
 * @property string $corps
 * @property string $building
 * @property string $letter
 * @property double $lat
 * @property double $lng
 * @property string $name
 * @property string $short_name
 * @property string $logo
 * @property string $description
 * @property integer $partner_id
 * @property string $partner_type
 * @property string $partner_temp_name
 * @property string $comment
 *
 * @property City $city
 * @property Club[] $clubs
 * @property Teacher[] $teachers
 * @property Model $partner
 */
class Organization extends Model implements FillableFromForm
{
    use CheckDelete;
    use HasFiles;
    use EditableEntity;
    use HasContacts;
    use HasNote;
    use Ownerable;

    // Формы собственности
    const OWNERSHIP_NATIONAL = 'national';
    const OWNERSHIP_PRIVATE = 'private';

    public static $ownerships = [
        self::OWNERSHIP_NATIONAL => 'Государственный',
        self::OWNERSHIP_PRIVATE => 'Частный',
    ];

    public $fieldsContainingFile = ['logo'];

    public $relatedEntities = ['clubs'];

    protected $table = 'clubs.organizations';

    protected $fillable = [
        'city_id',
        'ownership',
        'postal_code',
        'address',
        'street',
        'house',
        'corps',
        'building',
        'letter',
        'lat',
        'lng',
        'name',
        'short_name',
        'logo',
        'description',
        'partner_type',
        'partner_id',
        'comment',
        'partner_temp_name'
    ];

    public static function publicFields()
    {
        return [
            'id',
            'city_id',
            'ownership',
            'postal_code',
            'address',
            'street',
            'house',
            'corps',
            'building',
            'letter',
            'lat',
            'lng',
            'name',
            'short_name',
            'logo',
            'description',
            'partner_type',
            'partner_id',
            'partner_temp_name'
        ];
    }

    /**
     * @return array
     */
    public function getOwnersIds()
    {
        return $this->userOwner->pluck('id')->toArray();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clubs()
    {
        return $this->hasMany(Club::class, 'organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function partner()
    {
        return $this->morphTo();
    }

    /**
     * @return array
     */
    public static function getMorphableEntities()
    {
        $entities = [];

        foreach (AppServiceProvider::$classMap as $alias => $fullClassName) {
            if (in_array(HasPartners::class, class_uses($fullClassName))) {
                $entities[] = $alias;
            }
        }
        return $entities;
    }

    public function getLogoAttribute($value)
    {
        $path = File::getPath($this, $value);
        $path .= $value;
        return $path;
    }
}
