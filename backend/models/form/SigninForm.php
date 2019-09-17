<?php

declare(strict_types=1);

namespace app\models\form;

use Yii;
use yii\base\Model;

use app\models\User;

/**
 * Авторизация пользователя
 */
class SigninForm extends Model
{
    public $email;
    public $password;

    /**
     * @property null|User $user
     */
    private $_user = false;



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
        return [
            [['email', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }



    /**
     * Проверка пароля
     * @return void
     */
    public function validatePassword() : void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError(null, 'Incorrect email or password.');
            }
        }
    }



    /**
     * Вход в систему по вводимым данным
     * @return bool
     */
    public function login() : bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }

        Yii::warning('failure logged in');

        return false;
    }



    /**
     * Поиск пользователя по логину (почта или телефон)
     * @return null|User
     */
    private function getUser() : ?User
    {
        if (!$this->_user) {
            $this->_user = User::findIdentityByLogin($this->email);
        }

        return $this->_user;
    }

}
