<?php
declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property integer  id
 * @property string   name        название страны
 * @property string   iso_code    цифровой код
 * @property string   iso_code_2  2-ух символьный код (используется для пользователей)
 * @property string   iso_code_3  3-ех символьный код
 * @property boolean  status      статус (активно | удалено)
 * @property integer  sort        порядок сортировки
 */
class Country extends ActiveRecord
{

    const DEFAULT_CODE = 'RU';      // код страны по умолчанию

    const STATUS_INACTIVE = false;  // неактивная статья
    const STATUS_ACTIVE  = true;    // активная



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%countries}}';
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [['name', 'iso_code', 'iso_code_2', 'iso_code_3'], 'required'],
            [['name', 'iso_code', 'iso_code_2', 'iso_code_3'], 'trim'],
            [['name', 'iso_code', 'iso_code_2', 'iso_code_3'], 'unique'],
            ['name', 'string', 'max' => 255],
            ['iso_code', 'string', 'max' => 3],
            ['iso_code_2', 'string', 'max' => 2],
            ['iso_code_3', 'string', 'max' => 3],
        ];
    }

}
