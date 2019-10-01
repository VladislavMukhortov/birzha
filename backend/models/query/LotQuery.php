<?php
declare(strict_types=1);

namespace app\models\query;

use Yii;
use yii\db\ActiveQuery;

use app\models\Lot;

class LotQuery extends ActiveQuery
{


    /**
     * Обявление по уникальному url
     * @param  string $link url объявления
     * @return
     */
    public function byLink($link = '')
    {
        return $this->andWhere([
            'link' => $link
        ]);
    }


    /**
     * Объявление которое отображается в списке объявлений для всех пользователей
     * @return
     */
    public function active()
    {
        return $this->andWhere([
            'status' => Lot::STATUS_ACTIVE
        ]);
    }



    /**
     * @param  null $db
     * @return array|Lot[]
     */
    // public function all($db = null)
    // {
    //     return parent::all($db);
    // }



    /**
     * @param  null $db
     * @return array|null|Lot
     */
    // public function one($db = null)
    // {
    //     return parent::one($db);
    // }

}
