<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Readers;

/**
 * LoginForm is the model behind the login form.
 *
 *
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password.$user->ID)) {
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        $user = $this->getUser();

        if ($this->validate()){
            return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
        }
        return False;
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Readers::findByEmail($this->email);
        }

        if ($this->_user->Active == false) {
            Yii::$app->session->setFlash('error', "Your account has been suspended!");
            return false;
        }

        return $this->_user;
    }
}