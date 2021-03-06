<?php

namespace ra\models;

use Yii;
use yii\db\Expression;
use yii\web\HttpException;

/**
 * This is the model class for table "{{%user_key}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $key_value
 * @property string $create_time
 * @property string $consume_time
 * @property string $expire_time
 *
 * @property User $user
 */
class UserKey extends \yii\db\ActiveRecord
{
    const TYPE_EMAIL_CHANGE = 1;
    const TYPE_PASSWORD_CHANGE = 2;
    const TYPE_PASSWORD_RESET = 3;
    const TYPE_OTHER = 9;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_key}}';
    }

    public static function generate($type, $userId, $expire = 60 * 5)
    {

        $model = new self();
        $model->setAttributes([
            'user_id' => $userId,
            'type' => $type,
            'key_value' => uniqid(uniqid()),
            'create_time' => new Expression('now()'),
            'expire_time' => new Expression('FROM_UNIXTIME(' . (time() + $expire) . ')'),
        ]);
        if ($model->save())
            return $model->key_value;
        else
            throw new HttpException(400, print_r($model->errors, 1));

        return false;
    }

    public static function add($type, $key, $expire)
    {

        $search = [
            'type' => $type,
            'key_value' => $key,
        ];
        $model = self::findOne($search);
        if (!$model) $model = new self($search);
        $model->setAttributes([
            'user_id' => Yii::$app->user->id ?: ($model->user_id ? $model->user_id : 1),
            'expire_time' => is_int($expire) ? new Expression('FROM_UNIXTIME(' . $expire . ')') : $expire,
        ]);
        return $model->save(false);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'key_value'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['create_time', 'consume_time', 'expire_time'], 'safe'],
            [['key_value'], 'string', 'max' => 255],
            [['key_value'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'user_id' => Yii::t('ra', 'User ID'),
            'type' => Yii::t('ra', 'Type'),
            'key_value' => Yii::t('ra', 'Key Value'),
            'create_time' => Yii::t('ra', 'Create Time'),
            'consume_time' => Yii::t('ra', 'Consume Time'),
            'expire_time' => Yii::t('ra', 'Expire Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function behaviors()
    {
        return [
            [
                'class' => '\yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => false,
            ]
        ];
    }
}
