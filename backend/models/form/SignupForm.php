<?php

declare(strict_types=1);

namespace app\models\form;

use yii\base\Model;

use app\models\Country;
use app\models\User;

/**
 * Регистрация пользователя
 */
class SignupForm extends Model
{
    public $name;
    public $gender = 'u';
    public $email;
    public $phone = '';
    public $country_code;
    public $password;
    public $timezone = '0';



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'email' => 'E-Mail',
            'password' => 'Password'
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        $country = Country::find()
            ->select('iso_code_2')
            ->where(['status' => Country::STATUS_ACTIVE])
            ->column();

        return [
            [['name', 'gender', 'email', 'phone', 'country_code', 'password'], 'trim'],

            ['name', 'string', 'length' => [2, 255]],
            ['name', 'required', 'message' => 'Input name'],

            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email is not available'],
            ['email', 'required', 'message' => 'Input email'],

            ['country_code', 'in', 'range' => $country],
            ['country_code', 'required', 'message' => 'Укажите страну'],

            ['password', 'string', 'min' => 4, 'max' => 30],
            ['password', 'required', 'message' => 'Необходимо ввести пароль'],

            ['timezone', 'string'],

            // ['name', 'match', 'pattern' => '/^[a-z0-9_]{4,20}$/'],
            // ['password', 'match', 'pattern' => '/^[A-Za-z0-9_]{8,60}$/'],
        ];
    }



    /**
     * Регистрируем пользователя
     * @return null|User
     */
    public function signup() : ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User;
        $user->setName($this->name);
        $user->setGender($this->gender);
        $user->setLink();
        $user->setEmail($this->email);
        $user->setPhone($this->phone);
        $user->setCountryCode($this->country_code);
        $user->timezone = '+00:00';
        $user->language = app()->language;
        $user->setAccessToken();
        $user->setPassword($this->password);
        $user->save();

        return $user;
    }

}
