<?php

/**
 * отправка сообщения при изменении почтового ящика
 * $company Company контрагент
 */

// ссылка для подтверждения email
$verify_link = Yii::$app->params['frontendURL'] . 'auth/verify-email-company/' . $company->verify_email;

?>

<div>Здравствуйте, <?= h($company->name) ?>!</div>
<div>Что бы подтвердить почтовый адрес перейдите по ссылке</div>
<div><a href="<?= h($verify_link) ?>"><?= h($verify_link) ?></a></div>

<div>Ваши учетные данные:</div>
<div>Email: <?= h($company->email) ?></div>
