<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegistrationForm est le model derrière le formulaire d'inscription.
 *
 */
class RegistrationForm extends Model {

    // Données entrées par l'utilisateur pour se créer un compte
    public $nom;
    public $prenom;
    public $username;
    public $password;
    public $mail;
    public $permis;
    public $photo;
    public $rememberMe = true;

    //private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            
            // Attributs obligatoires pour s'inscrire
            ['nom', 'required', 'message' => 'Le nom doit être renseigné et ne peut pas être vide.'],
            ['prenom', 'required', 'message' => 'Le prénom doit être renseigné et ne peut pas être vide.'],
            ['username', 'required', 'message' => 'Le pseudo doit être renseigné et ne peut pas être vide.'],
            ['password', 'required', 'message' => 'Le mot de passe doit être renseigné et ne peut pas être vide.'],
            ['mail', 'required', 'message' => "L'adresse mail doit être renseigné et ne peut pas être vide."],
            ['photo', 'required', 'message' => "La photo doit être renseignée pour être utilisée comme photo de profil grâce à une URL."],

            // S'assure que le nom et le prénom sont bien des mots
            ['nom', 'string', 'message' => "Le nom doit être un mot."],
            ['prenom', 'string', 'message' => "Le prénom doit être un mot."],

            // Le nom d'utilisateur est validé par la méthode validatePseudo()
            ['username', 'string', 'min' => 4, 'max' => 30, 
                'tooShort' => "Le pseudo doit contenir au moins 4 caractères.",
                'tooLong' => "Le pseudo doit contenir maximum 30 caractères.",
                'message' => "Le pseudo doit contenir entre 4 et 30 caractères."
            ],
            ['username', 'validatePseudo'],

            // L'adresse mail est validée par la méthode validateMail()
            ['mail', 'email', 'message' => "L'adresse mail n'est pas au bon format."],
            ['mail', 'validateMail'],

            // La photo doit être une chaine de caractères = une URL
            ['photo', 'string', 'message' => 'Votre photo doit être une URL.'],

            // Le permis ne doit pas être obligatoirement renseigné
            ['permis', 'integer', 'message' => 'Le numéro de permis doit être un nombre entier contenant entre 1 et 12 chiffres.'],

            // rememberMe est un boolean
            ['rememberMe', 'boolean', 'message' => "Votre choix pour se souvenir de vous n'est pas au bon format."],

            // Le mot de passe doit avoir une longueur minimale de 4 (le plus petit mot de passe est 4)
            ['password', 'string', 'min' => 4, 
                'tooShort' => "Le mot de passe doit contenir au moins 4 caractères.",
                'message' => "Le mot de passe doit être une chaine de caractères."
            ],
            // Faire une règle ['password', 'validatePassword'] où on regarde si le mot de passe contient bien au moins 1 chiffre, 1 majuscule, 1 caractère spécial, ...

        ];
    }

    /**
     * Vérifie que le nom d'utilisateur n'est pas déjà utilisé par un autre utilisateur.
     */
    public function validatePseudo($attribute) {
        if(User::findByPseudo($this->$attribute)) $this->addError($attribute, "Nom d'utilisateur invalide.");
    }

    /**
     * Vérifie que l'adresse mail n'est pas déjà utilisée par un autre utilisateur.
     */
    public function validateMail($attribute) {
        if(User::findByMail($this->$attribute)) $this->addError($attribute, 'Adresse mail invalide.');
    }

    /**
     * Création d'un objet Internaute et l'insère dans la BDD.
     *
     * @return true|false
     */
    public function register() {

        if(!$this->validate()) return null; // Valide toutes les règles de la méthode rules()

        // Création d'une instance de la classe Internaute
        $internaute = new Internaute();

        // Ajout des valeurs des attributs dans l'instance
        $internaute->nom = $this->nom;
        $internaute->prenom = $this->prenom;
        $internaute->pseudo = $this->username;
        $internaute->pass = sha1($this->password);  // Hash du mot de passe avec sha1()
        $internaute->mail = $this->mail;
        $internaute->photo = $this->photo;
        $internaute->permis = $this->permis ?: null;

        if($internaute->save()) {   // Si l'internaute a bien été inséré dans la BDD
            // Connecte l'utilisateur au compte qu'il vient de crée
            $user = User::findByPseudo($this->username);
            Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            return true;
        }

        return false;

    }
}
