<?php 

namespace app\models; 

use yii\db\ActiveRecord; 

class Reservation extends ActiveRecord { 

    public static function tableName() { 
        return 'fredouil.reservation'; 
    } 

    /**
     * Récupère toutes les réservations effectuées par un utilisateur
     * 
     * @param id $id l'ID de l'utilisateur
     * @return Voyage[]|null
     */
    public static function findReservationsByUserId($id) {
        $reservations = Reservation::find()->where(['voyageur' => $id])->all();
        if($reservations) return $reservations;
        else return null;
    }

    /**
     * Récupère toutes les réservations à partir de l'ID du voyage
     * 
     * @param id_voyage $id_voyage l'ID du voyage
     * @return Voyage[]
     */
    public static function getReservationsByVoyageId($id_voyage) {
        $reservations = Reservation::find()->where(['voyage' => $id_voyage])->all();
        /*if($reservations) return $reservations;
        else return null;*/
        return $reservations;
    }

    /**
     * Affiche les informations d'une réservation
     * 
     * @param reservation Instance de la classe Reservation
     */
    public static function afficherInformations($reservation) {

        // Récupération de l'instance Voyage
        $voyage = Voyage::getVoyageById($reservation->id);

        // Récupération de l'instance Trajet (contient la ville de départ et d'arrivée)
        $trajet = Trajet::findTrajetById($voyage->trajet);
        echo "<h3>- Voyage de $trajet->depart à $trajet->arrivee</h3>";
        echo "<p>Distance : $trajet->distance km</p>";

        // Calcule la durée du trajet
        $duree = Trajet::calculerDuree($trajet->distance);
        echo "<p>Durée du trajet : $duree minutes</p>";

        echo "<p>Nombre de places réservées : $reservation->nbplaceresa</p>";

    }

    // ajouterReservation($id)

    // supprimerReservation($id)

} 

?>