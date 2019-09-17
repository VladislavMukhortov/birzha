<?php
declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property integer    id
 * @property integer    user_id     ID пользователя сделавший изменение
 * @property string     key         название параметра из БД который меняем
 * @property string     val         значение старого параметра
 * @property timestamp  created_at  дата создания
 */
class DataChange extends ActiveRecord
{

    /**
     * ключ     - ключ по которому получаем имя стоблца в БД при сохранении и получении данных
     * значения - имя столбца в БД по паттерну {table}--{column}
     */
    const KEYS = [
        'user_password' => 'user--password_hash',
        'user_name' => 'user--name',
        'user_email' => 'user--email',
        'user_phone' => 'user--phone',
        'user_position' => 'user--position',
        'company_location' => 'company--location',
        'company_bank_name' => 'company--bank_name',
        'company_bank_location' => 'company--bank_location',
        'company_email' => 'company--email',
        'company_phone' => 'company--phone',
        'company_site' => 'company--site',
        'company_director' => 'company--director',
        'company_text' => 'company--text',
    ];



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%data_change}}';
    }



    /**
     * @return array
     */
    public function behaviors() : array
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('UTC_TIMESTAMP()')
            ]
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            ['user_id', 'required'],
            [['key', 'val'], 'trim'],
            ['key', 'string', 'max' => 255],
            ['val', 'string', 'max' => 500],
        ];
    }



    /**
     * Сохранение изменяемых данных
     * @param string $key  название параметра из БД который меняем
     * @param mixed  $val  значение старого параметра
     */
    public function add(string $key, $val) : void
    {
        $user_id = (int) Yii::$app->user->identity->id;
        $key = mb_convert_encoding(strval($key), 'UTF-8', 'UTF-8');
        $val = mb_convert_encoding(strval($val), 'UTF-8', 'UTF-8');
        $val = mb_substr($val, 0, 500);

        if ($user_id && $key) {

            $data_change = new DataChange();
            DataChange::getDb()->transaction(function($db) use ($data_change,  $user_id, $key, $val) {
                $data_change->user_id = $user_id;
                $data_change->key = $key;
                $data_change->val = $val;
                $data_change->save();
            });
        }
    }

}
