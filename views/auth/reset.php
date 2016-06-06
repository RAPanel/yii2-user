<?php
/**
 * Created by PhpStorm.
 * User: Semyon
 * Date: 13.07.14
 * Time: 15:31
 */
use yii\helpers\Html;

?>

<div id="enter" class="forms">
    <div class="resetForm" id="reset">

            <? $form = \yii\widgets\ActiveForm::begin() ?>

            <?= $form->field($model, 'newPassword')->passwordInput(['placeholder'=>'Новый пароль'])->label(false) ?>

            <?= $form->field($model, 'newPasswordConfirm')->passwordInput(['placeholder'=>'Повторить пароль'])->label(false) ?>

            <?= Html::submitButton('сохранить', ['class' => 'button']) ?>

            <? $form->end() ?>


    </div>
</div>
