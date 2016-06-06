<?php
/**
 * Created by PhpStorm.
 * User: Semyon
 * Date: 13.07.14
 * Time: 15:31
 */
use yii\helpers\Html;

$this->title = 'Авторизация';
?>

<div id="enter" class="form">
    <div class="email" id="email">
        <h1 class="title"><?= $this->title ?></h1>

        <? $form = \yii\widgets\ActiveForm::begin(['action' => ['auth/login']]) ?>

        <?= $form->field($model, 'email')->input('email', ['placeholder' => 'e-mail'])->label(false) ?>

        <div class="text-center">
            <?= Html::submitButton('далее', ['class' => 'button white']) ?>
            <br>
            <?= Html::a('вход через вконтакте', ['auth/social', 'authclient' => 'vk'], ['class' => 'vk']) ?>
            <?= Html::a('вход через facebook', ['auth/social', 'authclient' => 'fb'], ['class' => 'fb']) ?>
            <br>
            <?= Html::a('регистрация', ['auth/register'], ['class' => 'recover']) ?>
        </div>

        <? $form->end() ?>
    </div>
</div>


