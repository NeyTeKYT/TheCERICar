<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm est le model derrière le formulaire de connexion.
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

            // Attributs obligatoires pour se connecter
            ['username', 'required', 'message' => 'Le pseudo doit être renseigné et ne peut pas être vide.'],
            ['password', 'required', 'message' => 'Le mot de passe doit être renseigné et ne peut pas être vide.'],

            // Le nom d'utilisateur doit avoir une certaine taille
            ['username', 'string', 'min' => 4, 'max' => 30, 
                'tooShort' => "Le pseudo doit contenir au moins 4 caractères.",
                'tooLong' => "Le pseudo doit contenir maximum 30 caractères.",
                'message' => "Le pseudo doit contenir entre 4 et 30 caractères."
            ],

            // Le mot de passe doit avoir une longueur minimale de 4 (le plus petit mot de passe est 4)
            ['password', 'string', 'min' => 4, 
                'tooShort' => "Le mot de passe doit contenir au moins 4 caractères.",
                'message' => "Le mot de passe doit être une chaine de caractères."
            ],

            // rememberMe est un boolean
            ['rememberMe', 'boolean', 'message' => "Votre choix pour se souvenir de vous n'est pas au bon format."],

            // Vérifie la correspondance du mot de passe avec celui présent dans la BDD
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
        if(!$this->hasErrors()) {
            $user = $this->getUser();
            if(!$user || !$user->validatePassword($this->password)) $this->addError($attribute, 'Pseudo ou mot de passe incorrect.');
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login() {
        if($this->validate()) return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() {
        if($this->_user === false) $this->_user = User::findByPseudo($this->username);
        return $this->_user;
    }
}
