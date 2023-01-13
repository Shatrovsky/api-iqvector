<?php


namespace App\Models\Common;


use App\Providers\AppServiceProvider;
use App\Traits\EditableEntity;
use App\Traits\HasContacts;
use App\Traits\HasNote;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Contact
 * @package App\Models\Common
 * @property integer $id
 * @property integer $essence_id
 * @property string $essence_type
 * @property string $description
 * @property string $type
 * @property string $content
 * @property string $content_extend
 * @property string $status
 */
class Contact extends Model
{
    protected $table = 'public.contacts';
    protected $fillable = [
        'essence_type',
        'essence_id',
        'description',
        'type',
        'content',
        'content_extend',
        'status'
    ];
    use EditableEntity;
    use HasNote;

    public const TYPE_PHONE = 'phone';
    public const TYPE_EMAIL = 'email';
    public const TYPE_SITE = 'site';
    public const TYPE_VKONTAKTE = 'vkontakte';
    public const TYPE_ODNOKLASSNIKI = 'odnoklassniki';
    public const TYPE_INSTAGRAM = 'instagram';
    public const TYPE_FACEBOOK = 'facebook';
    public const TYPE_YOUTUBE = 'youtube';
    public const TYPE_SKYPE = 'skype';

    public static $typesDictionary = [
        self::TYPE_PHONE => 'Телефон',
        self::TYPE_EMAIL => 'Электронная почта',
        self::TYPE_SITE => 'Сайт',
    ];

    public const STATUS_PUBLIC = 'public';
    public const STATUS_HIDDEN = 'hidden';
    public const STATUS_INTERNAL_USE = 'internal_use';

    public static $statusesDictionary = [
        self::STATUS_PUBLIC => 'Публичный',
        self::STATUS_HIDDEN => 'Скрытый',
        self::STATUS_INTERNAL_USE => 'Для внутреннего использования',
    ];

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
            if (in_array(HasContacts::class, class_uses($fullClassName))) {
                $entities[] = $alias;
            }
        }

        return $entities;
    }
}
