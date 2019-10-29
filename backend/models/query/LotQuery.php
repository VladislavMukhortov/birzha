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
     * Объявление которое пренадлежит пользователю
     * @return
     */
    public function my()
    {
        return $this->andWhere([
            'company_id' => Yii::$app->user->identity->company_id
        ]);
    }



    public function notDeleted()
    {
        return $this->andWhere([
            'status' => [
                Lot::STATUS_ARCHIVE,
                Lot::STATUS_WAITING,
                Lot::STATUS_ACTIVE,
                Lot::STATUS_COMMUNICATION,
                Lot::STATUS_COMPLETE,
            ]
        ]);
    }



    /**
     * Объявление которое можно редактировать
     * @return
     */
    public function hasEdit()
    {
        return $this->andWhere([
            'status' => [
                Lot::STATUS_ARCHIVE,
                Lot::STATUS_WAITING,
                Lot::STATUS_ACTIVE,
            ]
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
