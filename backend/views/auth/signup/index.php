<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = h('Регистрация');
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

<section class="py-5 bg-light signup">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-4 m-auto">
                <h2 class="text-center"><?php echo $this->title ?></h2>
                <p class="text-center">Пожалуйста, заполните следующие поля для регистрации:</p>

                <?php $form = ActiveForm::begin([
                    'id' => 'form-signup',
                    'options' => [
                        'enctype' => 'application/x-www-form-urlencoded'
                    ]
                ]); ?>

                    <?php echo $form->field($model, 'timezone', ['template' => '{input}', 'options' => ['class' => false]])
                        ->hiddenInput(['id' => 'timezone']) ?>

                    <?php echo $form->field($model, 'name')->textInput(['placeholder' => 'Input name']) ?>
                    <?php echo $form->field($model, 'email')->textInput(['placeholder' => 'Input email'])
                        ->hint('demo@demo.com') ?>
                    <?php echo $form->field($model, 'country_code')->dropDownList($country, ['class' => 'custom-select']) ?>

                    <?php echo $form->field($model, 'password')
                        ->passwordInput(['placeholder' => 'Придумайте пароль'])
                        ->hint('Пароль должен содержать не мене 6 символов') ?>

                    <div class="form-group">
                        <p class="text-center text-muted">Продолжая, Вы принимаете условия <?php echo Html::a('пользовательского соглашения', ['site/index']) ?>, <?php echo Html::a('правил', ['site/index']) ?>!</p>
                        <?php echo Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-success btn-block shadow']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
(function($) {
    // устанавливаем часовой пояс
    $('#timezone').val(new Date().getTimezoneOffset());
})(jQuery);
</script>
