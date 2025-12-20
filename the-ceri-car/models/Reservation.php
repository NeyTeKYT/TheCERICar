<?php 

namespace app\models; 

use yii\db\ActiveRecord; 
use yii\helpers\Html;

class Reservation extends ActiveRecord { 

    public static function tableName() { 
        return 'fredouil.reservation'; 
    } 

    public function rules() {
        return [
            [['nbplaceresa'], 'required'],
            [['nbplaceresa'], 'integer', 'min' => 1],
        ];
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

    public static function getReservationById($id) {
        $reservation = Reservation::find()->where(['id' => $id])->one();
        return $reservation;
    }

    /**
     * Affiche les informations d'une réservation
     * 
     * @param reservation Instance de la classe Reservation
     */

    public static function afficherInformations($reservation) {

        // Récupération du voyage correspondant à la réservation
        $voyage = Voyage::getVoyageById($reservation->voyage);

        // Récupération du trajet du voyage réservé
        $trajet = Trajet::findTrajetById($voyage->trajet);

        // Conducteur du voyage
        $conducteur = User::findIdentity($voyage->conducteur);

        // Véhicule utilisé pour le voyage
        $voiture = Voiture::getVoitureByIds($voyage->idtypev, $voyage->idmarquev);

        // Calcule la durée du voyage
        $duree = Trajet::calculerDuree($trajet->distance);

        // Calcule le tarif par personne et le tarif total pour ce voyage
        $tarif_par_personne = $voyage->tarif * $trajet->distance;
        $tarif_total = $tarif_par_personne * $reservation->nbplaceresa;

        // Récupérations de toutes les réservations pour ce voyage
        $reservations = Reservation::getReservationsByVoyageId($voyage->id);
        $nb_places_reservees = 0;
        foreach ($reservations as $resa) $nb_places_reservees += $resa->nbplaceresa;

        // Détermine le nombre de places restantes disponibles
        $nb_places_restantes = $voyage->nbplacedispo - $nb_places_reservees;

        // Affichage de la ville de départ et d'arrivée
        echo Html::tag('h4', Html::encode($trajet->depart) . ' ➜ ' . Html::encode($trajet->arrivee), ['class' => 'card-title mb-3']);

        echo Html::beginTag('div', ['class' => 'row']);

            echo Html::beginTag('div', ['class' => 'col-md-6']);

                // Affichage de l'heure de départ
                echo Html::tag('p', '<strong>Heure de départ : </strong>' . Html::encode($voyage->heuredepart) . 'h', ['class' => 'mb-1']);
                
                // Affichage de la distance entre la ville de départ et la ville d'arrivée (en km)
                echo Html::tag('p', '<strong>Distance : </strong>' . $trajet->distance . ' km', ['class' => 'mb-1']);

                // Affichage de la durée du voyage
                echo Html::tag('p', '<strong>Durée du voyage :</strong> ' . $duree . ' minutes');

                // Affichage du nombre de places réservées
                echo Html::tag('p', '<strong>Nombre de places réservées :</strong> ' . Html::encode($reservation->nbplaceresa), ['class' => 'mb-1']);

                // Affichage du nombre de places restantes disponibles 
                echo Html::tag('p', '<strong>Nombre de places restantes disponibles : </strong>' . Html::encode($nb_places_restantes), ['class' => 'mb-1']);

                // Affichage du nombre de bagages par personne
                echo Html::tag('p', '<strong>Nombre de bagages par personne : </strong>' . Html::encode($voyage->nbbagage), ['class' => 'mb-1']);

            echo Html::endTag('div');

            echo Html::beginTag('div', ['class' => 'col-md-6']);

                // Affichage du nom / prénom du conducteur qui propose ce voyage
                echo Html::tag('p', '<strong>Conducteur : </strong>' . Html::encode($conducteur->nom . ' ' . $conducteur->prenom), ['class' => 'mb-1']);

                // Affichage du véhicule utilisé pour ce voyage (marque / type)
                echo Html::tag('p', '<strong>Véhicule : </strong>' . Html::encode($voiture->marque . ' ' . $voiture->type), ['class' => 'mb-1']);

                // Affichage du prix par personne pour ce voyage 
                if($reservation->nbplaceresa > 1) echo Html::tag('p', '<strong>Tarif par personne : </strong>' . $tarif_par_personne . ' €', ['class' => 'mb-1']);

                // Affichage du prix total pour ce voyage 
                echo Html::tag('p', '<strong>Prix total pour ' . $reservation->nbplaceresa . ' personnes : </strong>' . $tarif_total . ' €', ['class' => 'mb-1']);

                // Si le voyage a des contraintes spécifiées par le conducteur
                if(!empty($voyage->contraintes)) echo Html::tag('p', '<strong>Contraintes : </strong>' . Html::encode($voyage->contraintes), ['class' => 'mb-1']);

            echo Html::endTag('div');

        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'mt-3 text-end']);

            // Affichage d'un bouton pour modifier la réservation
            echo Html::a('Modifier le nombre de places réservées', ['site/modifier-reservation', 'id' => $reservation->id], ['class' => 'btn btn-warning me-2']);

            // Affichage d'un bouton pour supprimer la réservation
            echo Html::button(
                'Supprimer la réservation',
                [
                    'class' => 'btn btn-danger supprimer-reservation',
                    'data-id' => $reservation->id,
                    'type' => 'button'
                ]
            );

        echo Html::endTag('div');

    }

    // ajouterReservation($id)

    // modifierReservation($id)

    // supprimerReservation($id)

} 

?>