<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\search\LotSearch;

/**
 *
 */
class BoardController extends Controller
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
     * Доска объявлений
     * @return string
     */
    public function actionIndex() : string
    {
        $search_model = new LotSearch();
        $data_provider = $search_model->search(Yii::$app->request->get());

        return $this->render('index', [
            'search_model' => $search_model,
            'data_provider' => $data_provider,
        ]);
    }

}
