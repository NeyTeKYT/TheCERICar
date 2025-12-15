<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegistrationForm is the model behind the registration form.
 *
 * @property-read User|null $user
 *
 */
class RegistrationForm extends Model {

    // Données entrées par l'utilisateur pour se connecter
    public $nom;
    public $prenom;
    public $username;
    public $password;
    public $mail;
    public $permis;
    public $photo;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // required attributes
            ['nom', 'required', 'message' => 'Le nom doit être renseigné et ne peut pas être vide.'],
            ['prenom', 'required', 'message' => 'Le prénom doit être renseigné et ne peut pas être vide.'],
            ['username', 'required', 'message' => 'Le pseudo doit être renseigné et ne peut pas être vide.'],
            ['password', 'required', 'message' => 'Le mot de passe doit être renseigné et ne peut pas être vide.'],
            ['mail', 'required', 'message' => "L'adresse mail doit être renseigné et ne peut pas être vide."],
            ['photo', 'required', 'message' => "La photo doit être renseignée pour être utilisée comme photo de profil grâce à une URL."],

            // username is validated by validatePseudo()
            ['username', 'string', 'min' => 4, 'max' => 30, 'message' => "Le pseudo doit contenir entre 4 et 30 caractères."],
            ['username', 'validatePseudo'],

            // mail is validated by validateMail()
            ['mail', 'email', 'message' => "L'adresse mail n'est pas au bon format."],
            ['mail', 'validateMail'],

            // Champs optionnels
            ['permis', 'integer', 'message' => 'Le numéro de permis doit être un nombre entier contenant entre 1 et 12 chiffres.'],
            ['photo', 'string'],

            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],

            // Because there are passwords with a length of 4 (test), adding a rule that requires special characters are useless in this case.
            // password is validated by validatePassword()
            ['password', 'string', 'min' => 4],
        ];
    }

    /**
     * Verify if the username has already been taken by someone else.
     */
    public function validatePseudo($attribute) {
        if(User::findByPseudo($this->$attribute)) {
            $this->addError($attribute, 'Ce nom d’utilisateur est déjà utilisé.');
        }
    }

    /**
     * Verify if the mail has already been taken by someone else.
     */
    public function validateMail($attribute) {
        if(User::findByMail($this->$attribute)) {
            $this->addError($attribute, 'Cette adresse mail est déjà utilisée.');
        }
    }

    /**
     * Creates a new row in the internaute table
     *
     * @return User|null
     */
    public function register() {
        if(!$this->validate()) return null;

        // Création d'une instance de la classe Internaute
        $internaute = new Internaute();
        $internaute->nom = $this->nom;
        $internaute->prenom = $this->prenom;
        $internaute->pseudo = $this->username;
        $internaute->pass = sha1($this->password);  // Hash du mot de passe avec sha1()
        $internaute->mail = $this->mail;
        $internaute->photo = $this->photo ?: null;
        $internaute->permis = $this->permis ?: null;

        if($internaute->save()) {
            $user = User::findByPseudo($this->username);
            Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            return true;
        }
        return false;

    }
}
