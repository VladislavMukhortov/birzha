<?php
declare(strict_types=1);

namespace app\models\query;

use Yii;
use yii\db\ActiveQuery;

use app\models\Crops;

class CropQuery extends ActiveQuery
{

    /**
     * Активная культура
     * @return
     */
    public function active()
    {
        return $this->andWhere([
            'status' => Crops::STATUS_ACTIVE
        ]);
    }

}
