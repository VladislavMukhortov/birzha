<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

use app\models\User;
use app\models\Lot;
use app\models\Crops;

/**
 *
 */
class DeclarationsController extends Controller
{

    /**
     * @return array
     */
    public function behaviors() : array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET']
                ]
            ]
        ];
    }



    /**
     * Страница объявления
     * @param  int    $link url объявления
     * @return string
     */
    public function actionIndex(string $link) : string
    {
        $declaration = Lot::find()
            ->where([
                'link' => $link,
                'status' => Lot::STATUS_ACTIVE
            ])
            ->one();

        if (!$declaration) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user = User::find()
            ->where(['id' => $declaration->user_id])
            ->one();

        return $this->render('index', [
            'declaration' => $declaration,
            'user' => $user,
            'crop' => Crops::findOne($declaration->crops_id)
        ]);
    }

}
