<?php


namespace App\Models\Common;


use App\Models\Locations\City;
use App\Providers\AppServiceProvider;
use App\Traits\HasAddress;
use Illuminate\Database\Eloquent\Model;
use Phaza\LaravelPostgis\Eloquent\PostgisTrait;
use Phaza\LaravelPostgis\Geometries\Point;

/**
 * Class Address
 * @package App\Models\Common
 *
 * @property string $essence_type
 * @property int $essence_id
 * @property integer $city_id
 * @property integer $postal_code
 * @property string $address
 * @property string $street
 * @property string $house
 * @property integer $corps
 * @property integer $building
 * @property string $letter
 * @property float $lat
 * @property float $lng
 *
 * @property Point $coords
 * @property mixed $owner
 * @property City $city
 */
class Address extends Model
{
    use PostgisTrait;

    protected $table = 'public.addresses';
    protected $fillable = [
        'essence_type',
        'essence_id',
        'city_id',
        'postal_code',
        'address',
        'street',
        'house',
        'corps',
        'building',
        'letter',
        'lat',
        'lng'
    ];

    protected $postgisFields = ['coords'];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->coords = new Point($model->lat, $model->lng);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner()
    {
        return $this->morphTo('essence');
    }


    /**
     * @return array
     */
    public static function getMorphableEntities()
    {
        $entities = [];

        foreach (AppServiceProvider::$classMap as $alias => $fullClassName) {
            if (in_array(HasAddress::class, class_uses($fullClassName))) {
                $entities[] = $alias;
            }
        }

        return $entities;
    }
}
