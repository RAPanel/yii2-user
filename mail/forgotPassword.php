<?php

use yii\helpers\Url;

/**
 * @var string $subject
 * @var \rere\user\models\User $user
 * @var \rere\user\models\UserKey $userKey
 */
?>

<h3><?= $subject ?></h3>

<p><?= Yii::t("user", "Please use this link to reset your password:") ?></p>

<p><?= Url::toRoute(["/user/reset", "key" => $userKey->key_value], true); ?></p>
