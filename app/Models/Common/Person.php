<?php


namespace App\Models\Common;


use App\Helpers\File;
use App\Providers\AppServiceProvider;
use App\Traits\EditableEntity;
use App\Traits\HasContacts;
use App\Traits\HasFiles;
use App\Traits\HasNote;
use App\Traits\HasPersons;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Person
 * @package App\Models\Common
 * @property integer $id
 * @property integer $person_type_id
 * @property string $essence_type
 * @property integer $essence_id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $description
 * @property string $position
 * @property string $photo
 *
 * @property PersonType $personType
 */
class Person extends Model
{
    use HasFiles;
    use EditableEntity;
    use HasNote;
    use HasContacts;

    public $fieldsContainingFile = ['photo'];

    protected $table = 'public.persons';

    protected $fillable = [
        'person_type_id',
        'essence_type',
        'essence_id',
        'first_name',
        'last_name',
        'middle_name',
        'description',
        'position',
        'photo'
    ];

    public function getPhotoAttribute($value)
    {
        $path = File::getPath($this, $value);
        $path .= $value;
        return $path;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner()
    {
        return $this->morphTo('essence');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function personType()
    {
        return $this->belongsTo(PersonType::class, 'person_type_id');
    }

    /**
     * @return array
     */
    public static function getMorphableEntities()
    {
        $entities = [];

        foreach (AppServiceProvider::$classMap as $alias => $fullClassName) {
            if (in_array(HasPersons::class, class_uses($fullClassName))) {
                $entities[] = $alias;
            }
        }

        return $entities;
    }
}
