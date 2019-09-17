<?php
declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property integer  id
 * @property string   name      название базиса
 * @property boolean  status    статус (активно | удалено)
 * @property integer  sort      порядок сортировки
 */
class Basis extends ActiveRecord
{

    const STATUS_INACTIVE = false;  // неактивный базис
    const STATUS_ACTIVE  = true;    // активный



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%basis}}';
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
            ['name', 'string', 'max' => 3],
        ];
    }

}
