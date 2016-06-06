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
    <h1 class="title">Регистрация</h1>
    <div class="registerForm" id="register">
        <? $form = \yii\widgets\ActiveForm::begin(['action' => ['auth/register']]) ?>

        <?= $form->field($model, 'username')->textInput(['placeholder' => 'имя'])->label(false) ?>

        <?= $form->field($model, 'email')->input('email', ['placeholder' => 'e-mail'])->label(false) ?>

        <div class="form-group">
            <?= $form->field($model, 'newPassword', ['options' => ['class' => 'password']])->passwordInput(['placeholder' => 'пароль'])->label(false) ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'newPasswordConfirm', ['options' => ['class' => 'repeat']])->passwordInput(['placeholder' => 'повторите пароль'])->label(false) ?>
        </div>

        <div class="text-center">
            <?= Html::submitButton('регистрация', ['class' => 'button white']) ?>
            <br>
            <?= Html::a('вход', ['auth/login'], ['class' => 'enter']) ?>
        </div>

        <? $form->end() ?>
    </div>
</div>
