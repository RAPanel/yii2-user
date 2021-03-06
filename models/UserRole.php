<?php

namespace ra\models;

use Yii;

/**
 * This is the model class for table "{{%user_role}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $updated_at
 * @property string $created_at
 * @property integer $can_admin
 *
 * @property User[] $users
 */
class UserRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['updated_at', 'created_at'], 'safe'],
            [['can_admin'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'name' => Yii::t('ra', 'Name'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
            'can_admin' => Yii::t('ra', 'Can Admin'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['role_id' => 'id']);
    }
}
