<?php

/**
 * отправка сообщения при сбросе (восстановлении) пароля
 * $user    User    пользователь
 */

// ссылка для сброса пароля
$password_reset_link = Yii::$app->params['frontendURL'] . 'auth/password-reset/' . $user->password_reset_token;

?>

<div>Здравствуйте, <?= h($user->name) ?>!</div>
<div>Перейдите по ссылке, чтобы сбросить пароль:</div>
<div><a href="<?= h($password_reset_link) ?>"><?= h($password_reset_link) ?></a></div>
