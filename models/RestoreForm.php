<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.05.2016
 * Time: 18:13
 */

namespace app\models;


use ra\admin\models\UserKey;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

class RestoreForm extends Model
{
    public $email;

    private $_user = false;

    public function rules()
    {
        return [
            ["email", "required"],
            ["email", "email"],
            ["email", "validateEmail"],
            ["email", "filter", "filter" => "trim"],
        ];
    }

    /**
     * Validate email exists and set user property
     */
    public function validateEmail()
    {
        // check for valid user
        $user = $this->getUser();
        if (!$user) {
            $this->addError("email", Yii::t("user", "Email not found"));
        } elseif ($user->status != $user::STATUS_ACTIVE) {
            $this->addError("email", Yii::t("user", "Email is inactive"));
        }
    }

    /**
     * Get user based on email
     *
     * @return \ra\admin\models\User|null
     */
    public function getUser()
    {
        // get and store user
        if ($this->_user === false) {
            $this->_user = User::findOne(['email' => $this->email]);
        }
        return $this->_user;
    }

    public function send()
    {
        if ($this->validate()) {
            $mail = Yii::$app->mailer->compose();
            $mail->setTo($this->email);
            $mail->setFrom([Yii::$app->params['fromEmail'] => Yii::$app->name]);
            $mail->setSubject('Запрос на восстановление пароля с сайта ' . Yii::$app->name);
            $mail->setTextBody($this->body);
            return $mail->send();
        }
        return false;
    }

    public function getBody()
    {
        $key = UserKey::generate(UserKey::TYPE_PASSWORD_RESET, $this->getUser()->id);
        return 'Для восстановления пароля необходимо перейти по ссылке ' . PHP_EOL . Url::to(['auth/reset', 'token' => $key], 1);
    }

}