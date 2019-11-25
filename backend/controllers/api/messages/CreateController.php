<?php
declare(strict_types=1);

namespace app\controllers\api\messages;

use Yii;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Response;

use yii\web\UploadedFile;

use app\models\form\messages\CreateTextMessage;
use app\models\form\messages\CreateFileMessage;

/**
 * API Получение списка сообщений
 */
class CreateController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors() : array
    {
        return ArrayHelper::merge(parent::behaviors(), [
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

            'authenticator' => [
                'class' => HttpHeaderAuth::className(),
                'except' => ['options']
            ]
        ]);
    }



    /**
     * {@inheritdoc}
     */
    public function beforeAction($action) : bool
    {
        Yii::$app->user->enableSession = false;
        return parent::beforeAction($action);
    }



    /**
     * @inheritdoc
     */
    public function actions() : array
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ],
        ];
    }



    /**
     * Создаем текстовое сообщение
     * @return string
     */
    public function actionTextMessage() : Response
    {
        $result = [
            'result' => 'error',
        ];

        $model = new CreateTextMessage();
        if ($model->load(Yii::$app->request->post(), '')) {
            $result = $model->save();
        }

        return $this->asJson($result);
    }



    /**
     * Загружаем файл
     * @return string
     */
    public function actionFileMessage() : Response
    {
        /*
         * https://serversideup.net/uploading-files-vuejs-axios/
         * http://qaru.site/questions/15390433/post-file-along-with-form-data-vue-axios
         * https://stackoverflow.com/questions/49478991/post-file-along-with-form-data-vue-axios
         * https://makitweb.com/how-to-upload-file-with-vue-js-and-php/
         */
        $result = [
            'result' => 'error',
        ];

        // File name
        $filename = $_FILES['file']['name'];

        // File extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $result['filename'] = $filename;
        $result['extension'] = $extension;

        /*$model = new CreateFileMessage();
        // if ($model->load(Yii::$app->request->post(), '')) {
            // $result = $model->save();
        // }

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->upload()) {
                // file is uploaded successfully
                return;
            }
        }*/

        return $this->asJson($result);
    }


}
