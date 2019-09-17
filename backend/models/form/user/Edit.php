<?php
declare(strict_types=1);

namespace app\models\form\user;

use Yii;
use yii\base\Model;

use app\models\User;
use app\models\DataChange;
use app\models\notice\EmailNotification;

/**
 * Редактирование данных пользователя
 */
class Edit extends Model
{
    public $member_name;
    public $member_phone;
    public $member_email;
    public $member_position;



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'member_name' => 'Имя',
            'member_phone' => 'Номер телефона',
            'member_email' => 'E-mail',
            'member_position' => 'Должность в компании',
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [
                [
                    'member_name',
                    'member_phone',
                    'member_email',
                ],
                'required',
            ],
            [
                [
                    'member_name',
                    'member_phone',
                    'member_email',
                    'member_position',
                ],
                'trim',
            ],
            [
                [
                    'member_name',
                    'member_phone',
                    'member_email',
                    'member_position',
                ],
                'default',
                'value' => '',
            ],
            ['member_name', 'string', 'max' => User::NAME_LENGTH_MAX, 'message' => 'Пожалуйста, укажите более короткий вариант имени'],
            ['member_email', 'string', 'max' => User::EMAIL_LENGTH_MAX, 'message' => 'Такой email не подойдет'],
            ['member_phone', 'string', 'max' => User::PHONE_LENGTH_MAX, 'message' => 'Недопустимый формат номера'],
            ['member_position', 'string', 'max' => User::POS_LENGTH_MAX],
        ];
    }



    /**
     * Редактирование данных пользователя
     * @return array
     */
    public function save() : array
    {
        $data = [
            'success' => false
        ];

        if (!$this->validate()) {
            $data['error'] = $this->getFirstErrors();
            return $data;
        }

        $user = User::findIdentity(Yii::$app->user->identity->id);
        if (!$user) {
            return $data;
        }

        $user_name = $user->name;
        $user_email = $user->email;
        $user_phone = $user->phone;
        $user_position = $user->position;

        User::getDb()->transaction(function($db) use ($user) {
            $user->setName($this->member_name);

            // меняем почту, есть она не подтверждена
            if ($user->verify_email) {
                $user->setEmail($this->member_email);
                $user->setVerifyEmail();
            }

            // меняем телефон если он не подтвержден
            if ($user->verify_phone) {
                $user->setPhone($this->member_phone);
                $user->setVerifyPhone();
            }

            $user->setPosition($this->member_position);
            $user->userChangedData();
            $user->save();
        });

        if ($user->hasErrors()) {
            /**
             * Ошибка валидации - тогда править модель
             * Ошибка сохранения - проблема с доступностью к БД
             */
            return $data;
        }

        if ($user_name !== $user->name) {
            DataChange::add(DataChange::KEYS['user_name'], $user_name);
        }

        if ($user_email !== $user->email) {
            DataChange::add(DataChange::KEYS['user_email'], $user_email);
            EmailNotification::userChangeEmail($user);
        }

        if ($user_phone !== $user->phone) {
            DataChange::add(DataChange::KEYS['user_phone'], $user_phone);
            /**
             * TODO: отправлять смс с кодом при подтверждении номера телефона
             */
        }

        if ($user_position && $user_position !== $user->position) {
            DataChange::add(DataChange::KEYS['user_position'], $user_position);
        }

        $data['success'] = true;
        $data['name'] = strval($user->name);
        $data['phone'] = strval($user->phone);
        $data['email'] = strval($user->email);

        return $data;
    }

}
