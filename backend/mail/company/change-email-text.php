<?php

/**
 * отправка сообщения при изменении почтового ящика
 * $company Company контрагент
 */

// ссылка для подтверждения email
$verify_link = Yii::$app->params['frontendURL'] . 'auth/verify-email-company/' . $company->verify_email;

?>

Здравствуйте, <?= h($company->name) ?>!

Что бы подтвердить почтовый адрес перейдите по ссылке
<?= h($verify_link) ?>


Ваши учетные данные:
Email: <?= h($company->email) ?>
