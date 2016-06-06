<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.05.2016
 * Time: 18:13
 */

namespace app\models;


use yii\base\Model;

class AuthForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
        ];
    }

}