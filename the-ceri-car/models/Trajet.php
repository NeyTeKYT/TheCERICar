<?php 

namespace app\models; 

use yii\db\ActiveRecord; 

class Trajet extends ActiveRecord { 

    public static function tableName() { 
        return 'fredouil.trajet'; 
    } 

    /**
     * Récupère le trajet à partir de son id
     * 
     * @param id $id l'ID du trajet
     * @return static|null
     */
    public static function findTrajetById($id) {
        $trajet = Trajet::find()->where(['id' => $id])->one();
        if($trajet) return $trajet;
        else return null;
    }

    /**
     * Récupère le trajet à partir de la ville de départ et de la ville d'arrivée
     * 
     * @param ville_d $ville_d la ville de départ
     * @param ville_a $ville_a la ville d'arrivée
     * @return static|null
     */
    public static function getTrajet($ville_d, $ville_a) {
        $trajet = Trajet::find()->where(['depart' => $ville_d, 'arrivee' => $ville_a])->one();
        if($trajet) return $trajet;
        else return null;
    }

    /**
     * Calcule la durée du trajet
     * 
     * @param distance Distance entre la ville de départ et la ville d'arrivée
     * @return int
     */
    public static function calculerDuree($distance) {
        return $distance;   // L'énoncé nous dit que 60km = 60 minutes
    }

    // afficherInformations($id)

    // modifierVilleDepart($ville_depart)

    // modifierVilleArrivee($ville_arrivee)

} 

?>