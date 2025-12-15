<?php

namespace app\models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface {
    
    // Attributs de la table "Internaute" (fredouil.internaute)
    public $id;
    public $username;
    public $password;
    public $nom;
    public $prenom;
    public $mail;
    public $photo;
    public $permis;

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return null;    // Je n'utiliserai pas cette méthode mais elle a besoin d'être déclarée
    }

    /**
     * Finds user by pseudo
     *
     * @param string $pseudo
     * @return static|null
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
     * Finds user by mail
     *
     * @param string $mail
     * @return static|null
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
     * {@inheritdoc}
     */
    public function getId() {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return null;    // Je n'utiliserai pas cette méthode mais elle a besoin d'être déclarée
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return null;    // Je n'utiliserai pas cette méthode mais elle a besoin d'être déclarée
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return sha1($password) === $this->password;
    }

    /**
     * Affiche les informations d'un utilisateur
     * 
     * @param user Instance de la classe User
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

    // modifierPrenom($id, $prenom)

    // modifierNom($id, $nom)

}
