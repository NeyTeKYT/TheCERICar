<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * MonCompteForm est le model derrière le formulaire pour modifier ses informations personnelles.
 * 
 */
class MonCompteForm extends Model {

    // Données entrées par l'utilisateur pour modifier ses informations
    public $username;
    public $password;        
    public $nom;
    public $prenom;
    public $mail;
    public $photo;
    public $permis;

    public $id; // ID de l'utilisateur chargé avec l'ID de session

   /**
     * @return array the validation rules.
     */
    public function rules() {
        return [

            // Attributs obligatoires pour modifier ses informations
            ['nom', 'required', 'message' => 'Le nom doit être renseigné et ne peut pas être vide.'],
            ['prenom', 'required', 'message' => 'Le prénom doit être renseigné et ne peut pas être vide.'],
            ['username', 'required', 'message' => 'Le pseudo doit être renseigné et ne peut pas être vide.'],
            ['mail', 'required', 'message' => "L'adresse mail doit être renseignée et ne peut pas être vide."],
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
        $user = User::findByPseudo($attribute);
        // Si un utilisateur existe et que ce n'est pas moi
        if($user && $user->id != $this->id) $this->addError($attribute, "Ce nom d'utilisateur est déjà utilisé.");
    }

    /**
     * Vérifie que l'adresse mail n'est pas déjà utilisée par un autre utilisateur.
     */
    public function validateMail($attribute) {
        $user = User::findByMail($attribute);
        // Si un utilisateur existe ET que ce n'est pas moi
        if ($user && $user->id != $this->id) $this->addError($attribute, "Cette adresse mail est déjà utilisée.");
    }

    /**
     * Charge les données dans l'instance depuis l'utilisateur connecté.
     */
    public function loadFromUser($user) {

        // Chargement des valeurs des attributs
        $this->username = $user->username;
        $this->nom = $user->nom;
        $this->prenom = $user->prenom;
        $this->mail = $user->mail;
        $this->photo = $user->photo;
        $this->permis = $user->permis;
    }

    /**
     * Création d'un objet Internaute et l'insère dans la BDD pour modifier l'utilisateur déjà crée.
     * 
     * @param User $user Utilisateur à modifier
     * @return true|false
     */
    public function update($user) {

        if(!$this->validate()) return null; // Valide toutes les règles de la méthode rules()

        $transaction = Yii::$app->db->beginTransaction();   // Lance une transaction pour effectuer plusieurs opérations dans la BDD

        try {

            $internaute = Internaute::findOne($user->id);   // Récupération de l'instance Internaute à partir de l'ID de l'utilisateur
            if(!$internaute) return false;  // Retourne directement false si l'internaute n'a pas bien été récupéré

            $current_permis = $internaute->permis;  // Valeur actuelle du permis de l'internaute

            // Ajout des valeurs des attributs dans l'instance
            $internaute->pseudo = $this->username;
            $internaute->nom = $this->nom;
            $internaute->prenom = $this->prenom;
            $internaute->mail = $this->mail;
            $internaute->photo = $this->photo;
            $internaute->permis = $this->permis ?: null;

            // Mise à jour du nouveau mot de passe si l'attribut envoyé n'est pas vide = modification voulue
            if(!empty($this->password)) $internaute->pass = sha1($this->password);

            $internaute->save();   // Insertion de l'utilisateur dans la BDD

            // Suppression de tous les voyages proposés par l'utilisateur si l'utilisateur souhaite enlever son permis
            if($current_permis !== null && empty($this->permis)) Voyage::deleteAll(['conducteur' => $user->id]);

            $transaction->commit();
            return true;

        } catch (\Throwable $e) {

            $transaction->rollBack();   // Retour en arrière = annule les modifications effectuées
            return false;
        }
    }

}