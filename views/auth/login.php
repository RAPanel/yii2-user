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
    <h1 class="title">Пароль</h1>
    <div class="enterForm" id="login">
        <? $form = \yii\widgets\ActiveForm::begin(['action' => ['auth/login']]) ?>

        <?= $form->field($model, 'username')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'пароль'])->label(false) ?>

        <div class="text-center">
            <?= Html::a('забыл пароль', ['auth/restore', 'email' => $model->username], ['class' => 'recover']) ?>
            <br>
            <?= Html::submitButton('вход', ['class' => 'button white']) ?>
            <br>
            <?= Html::a('< назад', ['auth/login'], ['class' => 'recover']) ?>
        </div>

        <? $form->end() ?>
    </div>
</div>

