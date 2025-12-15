<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model {

    // Données entrées par l'utilisateur pour se connecter
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // username and password are both required
            ['username', 'required', 'message' => 'Le pseudo doit être renseigné et ne peut pas être vide.'],
            ['password', 'required', 'message' => 'Le mot de passe doit être renseigné et ne peut pas être vide.'],

            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],

            // Because there are passwords with a length of 4 (test), adding a rule that requires special characters are useless in this case.
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
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Pseudo ou mot de passe incorrect.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $this->_user = User::findByPseudo($this->username);
        }

        return $this->_user;
    }
}
