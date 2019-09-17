<?php

declare(strict_types=1);

namespace app\controllers\admin;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;

class DashboardController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors() : array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get']
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    return $this->goHome();
                },
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }



    /**
     * устанавливаем layout админ панели для администратора
     * @return boolean
     */
    public function beforeAction($action) : bool
    {
        if (user()->can('admin')) {
            $this->layout = 'admin/main.php';
        }
        return parent::beforeAction($action);
    }



    /**
     * главная страница админ панели
     */
    public function actionIndex() : string
    {
        return $this->render('index.php');
    }

}
