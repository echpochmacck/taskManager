<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $token
 * @property int $role_id
 *
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{


    const SCENARIO_REGISTER = 'register';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email', 'on' => self::SCENARIO_REGISTER],
            ['email', 'unique', 'on' => self::SCENARIO_REGISTER],
            ['password', 'match', 'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{4,}$/", 'on' => self::SCENARIO_REGISTER],
            [['role_id'], 'integer'],
            [['email', 'password', 'token'], 'string', 'max' => 255],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'token' => 'Token',
            'role_id' => 'Role ID',
        ];
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        // return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        // return $this->authKey === $authKey;
    }
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    public function getIsAdmin(): bool
    {
        return $this->role_id == Role::getRoleId('admin');
    }
    public function register(): bool
    {
        $this->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->role_id = Role::getRoleId('user');
        return $this->save(false);
    }
    public function setToken()
    {
        $this->token = Yii::$app->security->generateRandomString();
        $this->save(false);
    }
}
