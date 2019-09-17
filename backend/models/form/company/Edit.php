<?php
declare(strict_types=1);

namespace app\models\form\company;

use Yii;
use yii\base\Model;

use app\models\Company;
use app\models\DataChange;
use app\models\notice\EmailNotification;

/**
 * Редактирование данных контрагента
 */
class Edit extends Model
{
    public $company_location;
    public $bank_name;
    public $bank_location;
    public $company_email;
    public $company_phone;
    public $company_site;
    public $company_director;
    public $company_text;



    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'company_location' => 'Адресс компании',
            'bank_name' => 'Название банка',
            'bank_location' => 'Адресс банка',
            'company_email' => 'E-mail',
            'company_phone' => 'Номер телефона',
            'company_site' => 'Адресс сайта',
            'company_director' => 'ФИО Директора',
            'company_text' => 'Дополнительная информация',
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
                    'company_location',
                    'bank_name',
                    'bank_location',
                    'company_email',
                    'company_phone',
                ],
                'required',
            ],
            [
                [
                    'company_location',
                    'bank_name',
                    'bank_location',
                    'company_email',
                    'company_phone',
                    'company_site',
                    'company_director',
                    'company_text',
                ],
                'trim',
            ],
            [
                [
                    'company_location',
                    'bank_name',
                    'bank_location',
                    'company_email',
                    'company_phone',
                    'company_site',
                    'company_director',
                    'company_text',
                ],
                'default',
                'value' => '',
            ],
            ['company_location', 'string', 'max' => Company::NAME_LENGTH_MAX],
            ['bank_name', 'string', 'max' => Company::NAME_LENGTH_MAX],
            ['bank_location', 'string', 'max' => Company::NAME_LENGTH_MAX],
            ['company_email', 'string', 'max' => Company::NAME_LENGTH_MAX],
            ['company_phone', 'string', 'max' => Company::NAME_LENGTH_MAX],
            ['company_site', 'string', 'max' => Company::NAME_LENGTH_MAX],
            ['company_director', 'string', 'max' => Company::NAME_LENGTH_MAX],
            ['company_text', 'string', 'max' => Company::TEXT_LENGTH_MAX],
        ];
    }



    /**
     * Редактирование данных контрагента
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

        $company = Company::findIdentity(Yii::$app->user->identity->company_id);
        if (!$company) {
            return $data;
        }

        $company_location = $company->location;
        $bank_name = $company->bank_name;
        $bank_location = $company->bank_location;
        $company_email = $company->email;
        $company_phone = $company->phone;
        $company_site = $company->site;
        $company_director = $company->director;
        $company_text = $company->text;

        Company::getDb()->transaction(function($db) use ($company) {
            $company->setLocation($this->company_location);
            $company->setBankName($this->bank_name);
            $company->setBankLocation($this->bank_location);

            // меняем почту, есть она не подтверждена
            if ($company->verify_email) {
                $company->setEmail($this->company_email);
                $company->setVerifyEmail();
            }

            // меняем телефон если он не подтвержден
            if ($company->verify_phone) {
                $company->setPhone($this->company_phone);
                $company->setVerifyPhone();
            }

            $company->setSite($this->company_site);
            $company->setDirector($this->company_director);
            $company->setText($this->company_text);
            $company->companyChangedData();
            $company->save();
        });

        if ($company->hasErrors()) {
            /**
             * Ошибка валидации - тогда править модель
             * Ошибка сохранения - проблема с доступностью к БД
             */
            return $data;
        }

        if ($company_location !== $company->location) {
            DataChange::add(DataChange::KEYS['company_location'], $company_location);
        }

        if ($bank_name !== $company->bank_name) {
            DataChange::add(DataChange::KEYS['company_bank_name'], $bank_name);
        }

        if ($bank_location !== $company->bank_location) {
            DataChange::add(DataChange::KEYS['company_bank_location'], $bank_location);
        }

        if ($company_email !== $company->email) {
            DataChange::add(DataChange::KEYS['company_email'], $company_email);
            EmailNotification::companyChangeEmail($company);
        }

        if ($company_phone !== $company->phone) {
            DataChange::add(DataChange::KEYS['company_phone'], $company_phone);
            /**
             * TODO: отправлять смс с кодом при подтверждении номера телефона
             */
        }

        if ($company_site && $company_site !== $company->site) {
            DataChange::add(DataChange::KEYS['company_site'], $company_site);
        }

        if ($company_director && $company_director !== $company->director) {
            DataChange::add(DataChange::KEYS['company_director'], $company_director);
        }

        if ($company_text && $company_text !== $company->text) {
            DataChange::add(DataChange::KEYS['company_text'], $company_text);
        }

        $data['success'] = true;

        return $data;
    }

}
