<?php
declare(strict_types=1);

namespace app\models\query;

use Yii;
use yii\db\ActiveQuery;

use app\models\Currency;

class CurrencyQuery extends ActiveQuery
{

    /**
     * Активная валюта которая используется на ресурсе
     * @return
     */
    public function active()
    {
        return $this->andWhere([
            'status' => Currency::STATUS_ACTIVE
        ]);
    }

}
