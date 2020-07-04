<?php
declare(strict_types=1);

namespace app\models\form\auth;

use Yii;
use yii\base\Model;

use app\models\User;
use app\models\Company;
use app\models\notice\EmailNotification;

/**
 * Регистрация пользователя
 */
class Signup extends Model
{
    public $member_name;
    public $member_phone;
    public $member_email;
    public $company_name;
    public $company_swift;
    public $company_iban;
    public $timezone;



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'member_name' => '',
            'member_phone' => '',
            'member_email' => '',
            'company_name' => '',
            'company_swift' => '',
            'company_iban' => '',
            'timezone' => '',
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [
                ['member_name','member_phone','member_email','company_name','company_swift','company_iban',],
                'required',
            ],
            [
                ['member_name','member_phone','member_email','company_name','company_swift','company_iban','timezone'],
                'trim',
            ],
            ['timezone', 'default', 'value' => '0',],
        ];
    }



    /**
     * Регистрация пользователя
     * @return array
     */
    public function create() : array
    {
        $result = [
            'result' => 'error',
        ];

        if (!$this->validate()) {
            $result['messages'] = $this->getFirstErrors();
            return $result;
        }

        $company = new Company();
        Company::getDb()->transaction(function($db) use ($company) {
			
            $company->setName($this->company_name);
            $company->setSWIFT($this->company_swift);
            $company->setIBAN($this->company_iban);
            $company->setVerifyEmail();
            $company->setVerifyPhone();
            $company->companyChangedData();
            $company->save();
        });

        if ($company->hasErrors()) {
            $result['messages'] = $company->getFirstErrors();
            return $result;
        }

        // пароль для пользователя
        $password = User::generatePassword();
        // $password = '1234567q';

        $user = new User();
        User::getDb()->transaction(function($db) use ($user, $company, $password) {
            $user->setName($this->member_name);
            $user->generateLink();
            $user->setEmail($this->member_email);
            $user->setPhone($this->member_phone);
            $user->company_id = $company->id;
            $user->timezone = convertTimeZoneFromJS($this->timezone);
            $user->language = Yii::$app->language;
            $user->setVerifyEmail();
            $user->setVerifyPhone();
            $user->setAccessToken();
            $user->setPassword($password);
            $user->save();
        });

        if ($user->hasErrors()) {
			//Company::find()->where(['id' => $company->id])->one()->delete();
            $result['messages'] = $user->getFirstErrors();
            return $result;
        }
		
		

        $result['result'] = 'success';
        // данные для авторизации не передаются так как авторизация проходит после подтверждения почты
        $result['name'] = $user->name;
        $result['email'] = $user->email;
        $result['phone'] = $user->phone;

        if (!EmailNotification::newUserRegistration($user, $password)) {
            /**
             * TODO: если сообщение не отправилось то надо что то сделать
             * например записать его на более позднюю отправку
             * или уведомить администратора
             * или уведомить пользователя, что доставка писем не работает
             * или удалить его аккаунт (но не компанию, вдруг в ней кто то есть)
             */
        }

        return $result;
    }

}
