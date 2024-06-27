<?php

namespace app\models;

use app\base\BaseActiveRecord;
use Yii;
use yii\web\IdentityInterface;

/**
 * @property integer $id
 * @property string $username
 * @property string $nick_name
 * @property string $password
 * @property string $mobile
 * @property string|null $access_token
 * @property string $email
 * @property integer $status
 * @property boolean $is_deleted
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $deleted_by
 * @property string $deleted_at
 */
class User extends BaseActiveRecord implements IdentityInterface
{

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): User
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): User
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return User
     */
    public static function findByUsername(string $username): User
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey():string
    {
        return $this->access_token;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->access_token === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function generateAccessToken(): string
    {
        $this->access_token = Yii::$app->security->generateRandomString(64);
        return $this->access_token;
    }
}
