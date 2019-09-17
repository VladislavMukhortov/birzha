<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Lot;

/**
 * фильтр для списка пользователей 'user/users'
 */
class LotSearch extends Model
{

    public $deal;           // тип объявления buy sell
    public $crops_id;       // ID культуры к которой пренадлежит объявление
    public $country_code;   // код страны объявления
    public $currency;       // код валюты



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['deal', 'crops_id', 'country_code', 'currency'], 'trim'],
            ['deal', 'in', 'range' => ['buy', 'sell']],
            ['country_code', 'in', 'range' => ['IR', 'TR', 'GE', 'AM', 'AZ', 'RU', 'KZ', 'UA']],
            ['currency', 'in', 'range' => ['USD', 'EUR', 'RUB']]
        ];
    }



    /**
     * вернет провайдер данных всех пользователей зарегистрированных на сайте
     * @param  array $params GET данные из запроса
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Lot::find()
            ->select('lots.*')
            ->where(['lots.status' => Lot::STATUS_ACTIVE])
            ->leftJoin('users', 'users.id = lots.user_id')
            ->leftJoin('crops', 'crops.id = lots.crops_id')
            ->with('users')
            ->with('crops')
            ->orderBy(['lots.id' => SORT_DESC]);

        // echo "<pre>";
        // print_r($query->limit(2)->all());
        // echo "</pre>";
        // die();

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Lot::LIMIT_ON_PAGE
            ]
        ]);

        // загружаем данные формы поиска и производим валидацию
        if (!($this->load($params) && $this->validate())) {
            return $data_provider;
        }

        if (!empty($this->deal)) {
            $query->andWhere(['lots.deal' => $this->deal]);
        }

        $crops_id = (int) $this->crops_id;
        if ($crops_id) {
            $query->andWhere(['lots.crops_id' => $crops_id]);
        }

        if (!empty($this->country_code)) {
            $query->andWhere(['lots.country_code' => $this->country_code]);
        }

        if (!empty($this->currency)) {
            $query->andWhere(['lots.currency' => $this->currency]);
        }

        return $data_provider;
    }

}
