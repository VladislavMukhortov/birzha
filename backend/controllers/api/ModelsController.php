<?php
declare(strict_types=1);

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Response;

/**
 * API
 */
class ModelsController extends Controller{

        public function behaviors() : array
    {
        $behaviors = [
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => Yii::$app->params['cors.origin'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600
                ],
            ],

            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST', 'OPTIONS']
                ]
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }
    
    public function actionSupport(){
        $data = Yii::$app->request->post();
        $data = json_decode(file_get_contents('php://input'), true);
        $name = strip_tags($data['name']);
        $email = strip_tags($data['email']);
        $text = strip_tags($data['text']);
        $message = "Email пользователя - " . $email . ", " . "Имя пользователя - " . $name . ": " . "текст сообщения - " . $text;
        Yii::$app->mailer->compose()
            ->setTo('heroesvforever@gmail.com')
            ->setSubject('Служба поддержки')
            ->setTextBody($message)
            ->send();
        return $data['name'];
    }
}