<?php
declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property integer    id
 * @property string     name             название компании
 * @property boolean    location         адресс компании
 * @property string     swift            Society for Worldwide Interbank Financial Telecommunications
 * @property string     iban             International Bank Account Number
 * @property string     bank_name        название банка
 * @property string     bank_location    адресс банка
 * @property string     email            email
 * @property string     phone            телефон
 * @property string     site             сайт
 * @property string     director         директор ФИО
 * @property string     text             дополнительная информация (не обязательный параметр)
 * @property boolean    is_edit          измененный
 * @property boolean    is_verify        проверенный
 * @property boolean    status           статус (активно | удалено)
 * @property string     verify_email     код для подтверждения email (NULL - подтвержден)
 * @property string     verify_phone     код для подтверждения номера телефона (NULL - подтвержден)
 * @property timestamp  created_at       дата создания
 * @property timestamp  updated_at       дата изменения
 */
class Company extends ActiveRecord
{

    const STATUS_INACTIVE = false;  // неактивная
    const STATUS_ACTIVE  = true;    // активная

    const NAME_LENGTH_MAX = 200;    // максимальная длина всех текстовых полей
    const IBAN_LENGTH_MAX = 60;     // максимальная длина кода International Bank Account Number
    const TEXT_LENGTH_MAX = 500;    // максимальная длина дополнительной информации
    const EMAIL_LENGTH_MAX = 100;   // максимальная длина почтового ящика
    const PHONE_LENGTH_MAX = 25;    // максимальная длина телефона

    const VERIFY_EMAIL_LENGTH = 32; // длина ключа для подтверждения почты
    const VERIFY_PHONE_LENGTH = 4;  // длина ключа для подтверждения телефона

    const TOKENS_LENGTH = 128;      // длина ключа для АПИ и восстановления пароля

    const LIMIT_ON_PAGE  = 10;      // кол-во записей на странице



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%company}}';
    }



    /**
     * @return array
     */
    public function behaviors() : array
    {
        return [
            [
                'class' => TimestampBehavior::className(),
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
            [
                ['name', 'swift', 'iban'],
                'required'
            ],
            [
                ['name', 'location', 'swift', 'iban', 'bank_name', 'bank_location', 'email', 'phone', 'site', 'director', 'text', 'verify_email', 'verify_phone'],
                'trim'
            ],
            [
                ['location', 'bank_name', 'bank_location', 'email', 'phone', 'site', 'director', 'text', 'verify_email', 'verify_phone'],
                'default',
                'value' => NULL
            ],
            ['name', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['location', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['swift', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['iban', 'string', 'max' => self::IBAN_LENGTH_MAX],
            ['bank_name', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['bank_location', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['email', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['phone', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['site', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['director', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['text', 'string', 'max' => self::TEXT_LENGTH_MAX],
            ['verify_email', 'string', 'max' => self::VERIFY_EMAIL_LENGTH],
            ['verify_phone', 'string', 'max' => self::VERIFY_PHONE_LENGTH],
        ];
    }



    /**
     * @param  int|string $id
     * @return null|static
     */
    public static function findIdentity($id) : ?self
    {
        return static::findOne(['id' => $id]);
    }



    /**
     * Устанавливаем название компании
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $name = mb_convert_encoding($name, 'UTF-8', 'UTF-8');
        $this->name = mb_substr($name, 0, self::NAME_LENGTH_MAX);
    }



    /**
     * Устанавливаем адресс компании
     * @param string $location
     */
    public function setLocation(string $location) : void
    {
        $location = mb_convert_encoding($location, 'UTF-8', 'UTF-8');
        $this->location = mb_substr($location, 0, self::NAME_LENGTH_MAX);
    }



    /**
     * Устанавливаем Society for Worldwide Interbank Financial Telecommunications
     * @param string $swift
     */
    public function setSWIFT(string $swift) : void
    {
        $swift = mb_convert_encoding($swift, 'UTF-8', 'UTF-8');
        $this->swift = mb_substr($swift, 0, self::NAME_LENGTH_MAX);
    }



    /**
     * Устанавливаем International Bank Account Number
     * @param string $iban
     */
    public function setIBAN(string $iban) : void
    {
        $iban = mb_convert_encoding($iban, 'UTF-8', 'UTF-8');
        $this->iban = mb_substr($iban, 0, self::IBAN_LENGTH_MAX);
    }



    /**
     * Устанавливаем название банка
     * @param string $bank_name
     */
    public function setBankName(string $bank_name) : void
    {
        $bank_name = mb_convert_encoding($bank_name, 'UTF-8', 'UTF-8');
        $this->bank_name = mb_substr($bank_name, 0, self::NAME_LENGTH_MAX);
    }



    /**
     * Устанавливаем адресс банка
     * @param string $bank_location
     */
    public function setBankLocation(string $bank_location) : void
    {
        $bank_location = mb_convert_encoding($bank_location, 'UTF-8', 'UTF-8');
        $this->bank_location = mb_substr($bank_location, 0, self::NAME_LENGTH_MAX);
    }



    /**
     * Устанавливаем почтовый ящик
     * @param string $email
     */
    public function setEmail(string $email) : void
    {
        $email = mb_convert_encoding($email, 'UTF-8', 'UTF-8');
        $email = mb_substr($email, 0, self::EMAIL_LENGTH_MAX);
        $email = strtolower($email);
        $this->email = preg_replace('/[^a-z0-9\.\@\_\-]/', '', $email);
    }



    /**
     * Устанавливаем номер телефона
     * @param string $phone
     */
    public function setPhone(string $phone) : void
    {
        $phone = mb_convert_encoding($phone, 'UTF-8', 'UTF-8');
        $phone = mb_substr($phone, 0, self::PHONE_LENGTH_MAX);
        $phone = strtolower($phone);
        $this->phone = preg_replace('/[^0-9\+]/', '', $phone);
    }



    /**
     * Устанавливаем адресс сайта
     * @param string $site
     */
    public function setSite(string $site) : void
    {
        $site = mb_convert_encoding($site, 'UTF-8', 'UTF-8');
        $site = mb_substr($site, 0, self::NAME_LENGTH_MAX);
        $this->site = strtolower($site);
    }



    /**
     * Устанавливаем ФИО директора
     * @param string $director
     */
    public function setDirector(string $director) : void
    {
        $director = mb_convert_encoding($director, 'UTF-8', 'UTF-8');
        $this->director = mb_substr($director, 0, self::NAME_LENGTH_MAX);
    }



    /**
     * Устанавливаем дополнительную информацию
     * @param string $text
     */
    public function setText(string $text) : void
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $this->text = mb_substr($text, 0, self::TEXT_LENGTH_MAX);
    }



        /**
     * Генерируем код для подтверждения почты
     */
    public function setVerifyEmail() : void
    {
        $this->verify_email = Yii::$app->security->generateRandomString(self::VERIFY_EMAIL_LENGTH);
    }



    /**
     * Генерируем код для подтверждения телефона
     */
    public function setVerifyPhone() : void
    {
        $first_char = rand(1, 9);
        $last_char = rand(0, 9);
        $code = strval($first_char . '0' . $first_char . $last_char);
        $this->verify_phone = mb_substr($code, 0, self::VERIFY_PHONE_LENGTH);
    }



    /**
     * Статус - информация изменена
     */
    public function companyChangedData() : void
    {
        $this->is_edit = true;
    }



    /**
     * Статус - информация проверена
     */
    public function companyDataVerified() : void
    {
        $this->is_edit = false;
    }

}
