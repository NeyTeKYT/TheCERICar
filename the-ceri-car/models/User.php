<?php

namespace app\models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface {
    
    // Attributs de la table "Internaute"
    public $id;
    public $username;
    public $password;
    public $nom;
    public $prenom;
    public $mail;
    public $photo;
    public $permis;

    /**
     * Récupère un internaute dans la BDD grâce à son ID.
     * 
     * @param int $id ID de l'utilisateur (clé primaire)
     * @return Internaute|null
     */
    public static function findIdentity($id) {
        $internaute = Internaute::findOne($id);
        if($internaute) {
            return new static([
                'id' => $internaute->id,
                'username' => $internaute->pseudo,
                'password' => $internaute->pass,
                'nom' => $internaute->nom,
                'prenom' => $internaute->prenom,
                'mail' => $internaute->mail,
                'photo' => $internaute->photo,
                'permis' => $internaute->permis,
            ]);
        }
        return null;
    }

    /**
     * Je n'utiliserai pas cette méthode mais elle a besoin d'être déclarée.
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return null;
    }

    /**
     * Récupère un utilisateur à partir de son pseudo.
     *
     * @param string $pseudo Pseudo de l'utilisateur (unique pour chaque enregistrement)
     * @return Internaute|null
     */
    public static function findByPseudo($pseudo) {
        $internaute = Internaute::find()->where(['pseudo' => $pseudo])->one();
        if($internaute) {
            return new static([
                'id' => $internaute->id,
                'username' => $internaute->pseudo,
                'password' => $internaute->pass,
                'nom' => $internaute->nom,
                'prenom' => $internaute->prenom,
                'mail' => $internaute->mail,
                'photo' => $internaute->photo,
                'permis' => $internaute->permis,
            ]);
        }
        return null;
    }

    /**
     * Récupère un utilisateur grâce à son adresse mail.
     *
     * @param string $mail Adresse mail de l'utilisateur (unique pour chaque enregistrement)
     * @return Internaute|null
     */
    public static function findByMail($mail) {
        $internaute = Internaute::find()->where(['mail' => $mail])->one();
        if($internaute) {
            return new static([
                'id' => $internaute->id,
                'username' => $internaute->pseudo,
                'password' => $internaute->pass,
                'nom' => $internaute->nom,
                'prenom' => $internaute->prenom,
                'mail' => $internaute->mail,
                'photo' => $internaute->photo,
                'permis' => $internaute->permis,
            ]);
        }
        return null;
    }

    /**
     * Retourn l'ID de l'utilisateur.
     * 
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Je n'utiliserai pas cette méthode mais elle a besoin d'être déclarée.
     */
    public function getAuthKey() {
        return null;    // Je n'utiliserai pas cette méthode mais elle a besoin d'être déclarée
    }

    /**
     * Je n'utiliserai pas cette méthode mais elle a besoin d'être déclarée.
     */
    public function validateAuthKey($authKey) {
        return null;    // Je n'utiliserai pas cette méthode mais elle a besoin d'être déclarée
    }

    /**
     * Vérifie si le mot de passe entré par l'utilisateur correspond bien au hash de l'utilisateur dans la BDD
     *
     * @param string $password Mot de passe à valider
     * @return true|false
     */
    public function validatePassword($password) {
        return sha1($password) === $this->password;
    }

    /**
     * Affiche les informations d'un utilisateur.
     * 
     * @param User $user Instance de la classe User
     */
    public static function afficherInformations($user) {

        echo "<h2>Informations sur $user->nom $user->prenom :</h2>";  // "L'ours Blanc" dans ce sens au lieu de "Blanc l'ours"
        echo "<p>Pseudo : $user->username</p>";
        echo "<p>Mot de passe : $user->password</p>";
        echo "<p>Adresse mail : $user->mail</p>";
        echo "<p>Photo : $user->photo</p>";

        // Vérifie si l'utilisateur a enregistré son numéro de permis = conducteur, ou pas
        if($user->permis) echo "<p>Numéro de permis : $user->permis</p>";
        else echo "<p>N'a pas le permis</p>";

    }

}
