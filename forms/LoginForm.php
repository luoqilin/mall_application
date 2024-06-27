<?php

namespace app\forms;

use app\base\BaseModel;
use app\models\User;
use Yii;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends BaseModel
{
    public string $username;
    public string $password;
    /**
     * @var array|mixed|string
     */
    public mixed $accessToken;

    private ?User $_user = null;


    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword(string $attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return array whether the user is logged in successfully
     */
    public function login(): array
    {
        if ($this->validate()) {
            $accessToken = $this->_user->generateAccessToken();
            Yii::$app->user->login($this->getUser(), 3600*24*30);
            $this->_user->save();
            return $this->success([
                'access_token' => $accessToken,
            ]);
        }
        return $this->failure(401, 'Incorrect username or password.');
    }

    public function loginToken(): void
    {
        $user = $this->getUserByToken();
        if ($user) {
            Yii::$app->user->login($user, 3600*24*30);
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    private function getUserByToken(): User|null
    {
        if ($this->_user === null) {
            $this->_user = User::findIdentityByAccessToken($this->accessToken);
        }

        return $this->_user;
    }
}
