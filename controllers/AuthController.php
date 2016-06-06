<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 19.02.2016
 * Time: 10:39
 */

namespace app\controllers;


use app\models\AuthForm;
use app\models\RegisterForm;
use app\models\RestoreForm;
use app\models\User;
use ra\admin\controllers\Controller;
use ra\admin\models\forms\LoginForm;
use ra\admin\models\Photo;
use ra\admin\models\UserAuth;
use ra\admin\models\UserKey;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\HttpException;

class AuthController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['logout', 'success'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register', 'restore', 'social', 'reset'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return [
            'social' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
                'successUrl' => Url::to(['success']),
            ],
        ];
    }

    public function actionLogin($back = null)
    {
        $model = new AuthForm();
        $login = new LoginForm();

        if ($login->load(Yii::$app->request->post())) {
            if ($login->login(60 * 60 * 24 * 30))
                return $this->goBack(is_null($back) ? ['site/index'] : $back);
            else
                return $this->render('login', ['model' => $login]);
        } elseif ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(['email' => $model->email]);
            if ($user)
                return $this->render('login', ['model' => new LoginForm(['username' => $model->email])]);
            else
                return $this->render('register', ['model' => new RegisterForm(['email' => $model->email])]);
        } elseif(!$back)
            Yii::$app->getUser()->setReturnUrl(Yii::$app->request->referrer);


        return $this->render('email', compact('model'));
    }

    /**
     * Register user
     */
    public function actionRegister($back = null)
    {
        $model = new RegisterForm(['role_id' => 2, 'status' => 1]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->user->login($model, 60 * 60 * 24 * 30);
            return $this->goBack(is_null($back) ? ['site/index'] : $back);
        }

        return $this->render('register', compact('model'));
    }

    /**
     * Log user out and redirect
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionRestore($email = null)
    {
        $model = new RestoreForm(compact('email'));

        if (($model->load(Yii::$app->request->post()) || $model->email) && $model->send()) {
            Yii::$app->session->setFlash('info', "Пароль отправлен на {$model->email}.");
            return $this->redirect(['auth/restore']);
        }

        return $this->render('restore', compact('model'));
    }

    public function actionReset($token)
    {
        $userKey = UserKey::findOne(['key_value' => $token]);
        if (!$userKey)
            throw new HttpException(403, 'Ключ не найден');
        elseif (strtotime($userKey->expire_time) < time())
            throw new HttpException(403, 'Ключ истек');
        elseif ($userKey->consume_time)
            throw new HttpException(403, 'Ключ был использован');

        $model = User::findOne($userKey->user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $userKey->consume_time = date('Y-m-d H:i:s');
            $userKey->save(false);
            return $this->goBack(['auth/login']);
        }

        return $this->render('reset', compact('model'));
    }

    /**
     * @param $client \yii\authclient\clients\YandexOAuth
     */
    public function onAuthSuccess($client)
    {
        $search = [
            'provider' => $client->getId(),
            'provider_id' => (string)$client->userAttributes['id'],
        ];
        if ($auth = UserAuth::findOne($search)) {
            $user = User::findOne($auth->user_id);
        } else {
            if (!($user = User::findOne(['email' => $client->userAttributes['email']]))) {
                $nameList = [];
                foreach (['last_name', 'first_name', 'name'] as $name)
                    if (!empty($client->userAttributes[$name]))
                        $nameList[] = $client->userAttributes[$name];

                $photo = '';
                foreach (['photo_max_orig'] as $name)
                    if (!$photo && !empty($client->userAttributes[$name]))
                        $photo = $client->userAttributes[$name];

                $user = new User([
                    'username' => implode(' ', $nameList),
                    'email' => $client->userAttributes['email'],
                    'password' => $password = uniqid(),
                ]);

                if (!$user->save()) {
                    VarDumper::dump($user->errors, 10, 1);
                    die;
                }

                Photo::add($photo, implode(' ', $nameList), $user->id, ['model' => $user::tableName()]);
            }
        }

        if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
            if (!$auth) $auth = new UserAuth($search);
            $auth->setAttributes([
                'user_id' => Yii::$app->user->id,
                'provider_attributes' => serialize($client->accessToken->params),
            ]);
            if (!$auth->save()) {
                VarDumper::dump($auth->errors, 10, 1);
                die;
            }
        }
    }

    public function actionSuccess()
    {
        return $this->goBack(['site/index']);
    }

}