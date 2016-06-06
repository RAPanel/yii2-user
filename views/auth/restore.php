<?php
/**
 * Created by PhpStorm.
 * User: Semyon
 * Date: 13.07.14
 * Time: 15:31
 */
use yii\helpers\Html;

?>

<div id="enter" class="form">
    <h1 class="title">Восстановление пароля</h1>
    <div class="enterForm" id="login">

        <? if (Yii::$app->session->hasFlash('info')): ?>
            <div class="text-center" style="color: #fff"><?= Yii::$app->session->getFlash('info') ?></div>

            <div class="text-center">
                <?= Html::a('авторизоваться', ['auth/login'], ['class' => 'login']) ?>
            </div>

        <? else: ?>

            <? $form = \yii\widgets\ActiveForm::begin(['action' => ['auth/login']]) ?>

            <?= $form->field($model, 'email')->label(false) ?>

            <div class="text-center">
                <?= Html::submitButton('сбросить', ['class' => 'button white']) ?>
                <br>
                <?= Html::a('< назад', ['auth/login'], ['class' => 'recover']) ?>
            </div>

            <? $form->end() ?>

        <? endif ?>

    </div>
</div>
