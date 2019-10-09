<?php
declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

use app\models\query\CurrencyQuery;

/**
 * @property integer  id
 * @property string   name          название валюты
 * @property string   iso_code      цифровой код
 * @property string   iso_code_3    3-ех символьный код (используется для пользователей)
 * @property boolean  status        статус (активно | удалено)
 * @property integer  sort          порядок сортировки
 */
class Currency extends ActiveRecord
{

    const STATUS_INACTIVE = false;  // неактивная валюта
    const STATUS_ACTIVE  = true;    // активная



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%currency}}';
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [['name', 'iso_code', 'iso_code_3'], 'required'],
            [['name', 'iso_code', 'iso_code_3'], 'trim'],
            [['name', 'iso_code', 'iso_code_3'], 'unique'],
            ['name', 'string', 'max' => 255],
            ['iso_code', 'string', 'max' => 3],
            ['iso_code_3', 'string', 'max' => 3],
        ];
    }



    /**
     * @return CurrencyQuery
     */
    public static function find()
    {
        return new CurrencyQuery(get_called_class());
    }

}
