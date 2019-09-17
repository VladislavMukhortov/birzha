<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = h('Вход');
$this->registerMetaTag(['name' => 'description', 'content' => '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => '']);
$this->registerMetaTag(['name' => 'og:title', 'content' => '']);
$this->registerMetaTag(['name' => 'og:type', 'content' => 'website']);
$this->registerMetaTag(['name' => 'og:image', 'content' => '']);
$this->registerMetaTag(['name' => 'og:url', 'content' => Url::to([request()->resolve()[0], 'language' => app()->language], true)]);
$this->registerMetaTag(['name' => 'og:locale', 'content' => app()->language]);
$this->registerMetaTag(['name' => 'og:description', 'content' => '']);
$this->registerMetaTag(['name' => 'og:site_name', 'content' => app()->name]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to([request()->resolve()[0], 'language' => app()->language], true)]);
$this->registerLinkTag(['rel' => 'image_src', 'href' => '']);

?>
<section class="py-5 bg-light signin">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-4 m-auto">
                <h2 class="text-center"><?= $this->title ?></h2>
                <p class="text-center">Введите данные, указанные при регистрации</p>
                <p>demo1@demo.com</p>
                <p>demo2@demo.com</p>
                <p>...</p>
                <p>demo3000@demo.com</p>
                <p>password: <b>1234567q</b></p>

                <?php $form = ActiveForm::begin([
                    'id' => 'form-signin',
                    'options' => [
                        'enctype' => 'application/x-www-form-urlencoded'
                    ]
                ]); ?>

                    <?= $form->field($model, 'email')->textInput(['placeholder' => 'Input email']) ?>
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Input password']) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Войти', ['class' => 'btn btn-success btn-block shadow']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</section>
