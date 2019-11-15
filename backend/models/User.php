<?php
declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * @property integer    id
 * @property string     name                    имя
 * @property string     link                    ссылка
 * @property string     email                   email
 * @property string     phone                   телефон
 * @property string     position                должность
 * @property integer    company_id              id компании к которой привязан пользователь
 * @property boolean    is_edit                 измененный
 * @property boolean    status                  статус (активно | удалено)
 * @property string     timezone                часовой пояс
 * @property string     language                язык приложения
 * @property string     verify_email            код для подтверждения email (NULL - подтвержден)
 * @property string     verify_phone            код для подтверждения номера телефона (NULL - подтвержден)
 * @property string     access_token            ключ авторизации max lebght 128
 * @property string     password_hash           хеш пароля
 * @property string     password_reset_token    ключ для восстановления пароля
 * @property timestamp  created_at              дата создания
 * @property timestamp  updated_at              дата изменения
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = false;  // удаленый пользователь
    const STATUS_ACTIVE  = true;    // активное

    const NAME_LENGTH_MAX = 100;    // максимальная длина имени
    const EMAIL_LENGTH_MAX = 100;   // максимальная длина почтового ящика
    const PHONE_LENGTH_MAX = 25;    // максимальная длина телефона
    const POS_LENGTH_MAX = 100;     // максимальная длина телефона

    const LINK_LENGTH_MAX = 50;     // максимальная длина ссылки пользователя
    const LINK_LENGTH = 15;         // длина ссылки пользователя по умолчанию

    const PSSW_LENGTH_DEFAULT = 8;  // длина генерируемого пароля
    const PSSW_LENGTH_MIN = 6;      // минимальная длина пароля

    const VERIFY_EMAIL_LENGTH = 32; // длина ключа для подтверждения почты
    const VERIFY_PHONE_LENGTH = 4;  // длина ключа для подтверждения телефона

    const TOKENS_LENGTH = 128;      // длина ключа для АПИ и восстановления пароля



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%users}}';
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
                ['name', 'link', 'email', 'phone'],
                'required'
            ],
            [
                ['name', 'link', 'email', 'phone', 'position', 'timezone', 'language', 'verify_email', 'verify_phone', 'access_token', 'password_hash'],
                'trim'
            ],
            [
                ['position', 'company_id', 'verify_email', 'verify_phone', 'password_reset_token'],
                'default',
                'value' => NULL
            ],

            ['email', 'unique', 'message' => 'К сожалению, E-mail занят'],
            ['phone', 'unique', 'message' => 'К сожалению, номер телефона занят'],

            ['name', 'string', 'max' => self::NAME_LENGTH_MAX],
            ['link', 'string', 'max' => self::LINK_LENGTH_MAX, 'message' => 'Такое имя не подойдет'],
            ['email', 'string', 'max' => self::EMAIL_LENGTH_MAX],
            ['phone', 'string', 'max' => self::PHONE_LENGTH_MAX],
            ['position', 'string', 'max' => self::POS_LENGTH_MAX],
            ['timezone', 'string', 'max' => 20],
            ['language', 'string', 'max' => 5],
            ['verify_email', 'string', 'max' => self::VERIFY_EMAIL_LENGTH],
            ['verify_phone', 'string', 'max' => self::VERIFY_PHONE_LENGTH],
            ['access_token', 'string', 'max' => self::TOKENS_LENGTH],
            ['password_hash', 'string', 'max' => 255],
            ['password_reset_token', 'string', 'max' => 255],
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
     * @param  string $link
     * @return null|static
     */
    public static function findIdentityByLink(string $link) : ?self
    {
        return static::findOne(['link' => $link]);
    }



    /**
     * @param  string $email
     * @return null|static
     */
    public static function findIdentityByEmail(string $email) : ?self
    {
        return static::findOne(['email' => $email]);
    }



    /**
     * @param  string $login
     * @return null|static
     */
    public static function findIdentityByLogin(string $login) : ?self
    {
        return static::find()
            ->where(['and', ['or', ['email' => $login], ['phone' => $login]]])
            ->one();
    }



    /**
     * @param  string $token
     * @return null|static
     */
    public static function findIdentityByVerifyEmailToken(string $token) : ?self
    {
        return static::findOne(['verify_email' => $token]);
    }



    /**
     * @param  mixed $token
     * @return null|static
     */
    public static function findIdentityByAccessToken($token, $type = null) : ?self
    {
        return static::findOne(['access_token' => $token]);
    }



    /**
     * Finds user by password reset token
     * @param string $token password reset token
     * @return null|static
     */
    public static function findIdentityByPasswordResetToken(string $token) : ?self
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(['password_reset_token' => $token]);
    }



    /**
     * Finds out if password reset token is valid
     * @param string $token password reset token
     * @return bool
    */
    public function isPasswordResetTokenValid($token) : bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }



    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        // return $this->auth_key;
    }



    /**
     * @return bool
     */
    public function validateAuthKey($auth_key)
    {
        // return $this->getAuthKey() === $auth_key;
    }



    /**
     * Validates password
     * @param string $password password to validate
     * @return bool
     */
    public function validatePassword(string $password) : bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }



    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     */
    public function setPassword(string $password) : void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }



    /**
     * Создаем токен для API
     * max lenght 128
     */
    public function setAccessToken() : void
    {
        $this->access_token = Yii::$app->security->generateRandomString(self::TOKENS_LENGTH);
    }



    /**
     * Создаем токен для сброса пароля
     */
    public function generatePasswordResetToken() : void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString(self::TOKENS_LENGTH) . '_' . time();
    }



    /**
     * Удаляем токен для сброса пароля
     */
    public function removePasswordResetToken() : void
    {
        $this->password_reset_token = null;
    }






    /**
     * Устанавливаем имя
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $name = mb_convert_encoding($name, 'UTF-8', 'UTF-8');
        $this->name = mb_substr($name, 0, self::NAME_LENGTH_MAX);
    }



    /**
     * Генерируем ссылку
     */
    public function generateLink() : void
    {
        $t = self::tableName();
        $link = '';
        do {
            $link = Yii::$app->security->generateRandomString(self::LINK_LENGTH);
            $user_id = Yii::$app->db->createCommand("SELECT id FROM {$t} WHERE link=:link")
                ->bindValue(':link', $link)
                ->queryScalar();
        } while ($user_id);

        $this->link = $link;
    }


    /**
     * Устанавливаем почтовый ящик
     * @param string $email
     */
    public function setEmail(string $email) : void
    {
        $this->email = self::cleanEmail($email);
    }



    /**
     * Отчищаем строку с почтовым адресом
     * @param  string $email
     * @return string
     */
    public function cleanEmail(string $email) : string
    {
        $email = mb_convert_encoding(strval($email), 'UTF-8', 'UTF-8');
        $email = mb_substr($email, 0, self::EMAIL_LENGTH_MAX);
        $email = strtolower($email);
        $email = preg_replace('/[^a-z0-9\.\@\_\-]/', '', $email);
        return $email;
    }



    /**
     * Устанавливаем номер телефона
     * @param string $phone
     */
    public function setPhone(string $phone) : void
    {
        $this->phone = self::cleanPhoneNumber($phone);
    }



    /**
     * Отчищаем строку с номером телефона
     * @param  string $phone
     * @return string
     */
    public function cleanPhoneNumber(string $phone) : string
    {
        $phone = mb_convert_encoding(strval($phone), 'UTF-8', 'UTF-8');
        $phone = mb_substr($phone, 0, self::PHONE_LENGTH_MAX);
        $phone = strtolower($phone);
        $phone = preg_replace('/[^0-9\+]/', '', $phone);
        return $phone;
    }





    /**
     * Устанавливаем должность
     * @param string $position
     */
    public function setPosition(string $position) : void
    {
        $position = mb_convert_encoding($position, 'UTF-8', 'UTF-8');
        $this->position = mb_substr($position, 0, self::POS_LENGTH_MAX);
    }



    /**
     * Генерируем пароль для пользователя
     * @return string
     */
    public static function generatePassword() : string
    {
        return Yii::$app->security->generateRandomString(self::PSSW_LENGTH_DEFAULT);
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
    public function userChangedData() : void
    {
        $this->is_edit = true;
    }



    /**
     * Статус - информация проверена
     */
    public function userDataVerified() : void
    {
        $this->is_edit = false;
    }


}
