<?php
declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property integer  id
 * @property string   name      название культуры
 * @property boolean  status    статус (активно | удалено)
 * @property integer  sort      порядок сортировки
 */
class Crops extends ActiveRecord
{

    const STATUS_INACTIVE = false;  // неактивная статья
    const STATUS_ACTIVE  = true;    // активная



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%crops}}';
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            ['name', 'required'],
            ['name', 'trim'],
            ['name', 'unique'],
            ['name', 'string', 'max' => 255],
        ];
    }

}
