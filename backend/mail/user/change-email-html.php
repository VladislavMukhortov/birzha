<?php

/**
 * отправка сообщения при изменении почтового ящика
 * $user      User   пользователь
 */

// ссылка для подтверждения email
$verify_link = Yii::$app->params['frontendURL'] . 'auth/verify-email/' . $user->verify_email;

?>

<div>Здравствуйте, <?= h($user->name) ?>!</div>
<div>Что бы подтвердить почтовый адрес перейдите по ссылке</div>
<div><a href="<?= h($verify_link) ?>"><?= h($verify_link) ?></a></div>

<div>Ваши учетные данные:</div>
<div>Email: <?= h($user->email) ?></div>
<div>Phone: <?= h($user->phone) ?></div>
