<?php 

namespace app\models; 

use yii\db\ActiveRecord; 
use yii\helpers\Html;

class Voyage extends ActiveRecord { 

    public static function tableName() { 
        return 'fredouil.voyage'; 
    } 

    /**
     * Récupère tous les voyages proposés par un conducteur
     * 
     * @param id $id l'ID du conducteur
     * @return Voyage[]|null
     */
    public static function findVoyagesByUserId($id) {
        $voyages = Voyage::find()->where(['conducteur' => $id])->all();
        if($voyages) return $voyages;
        else return null;
    }

    /**
     * Récupère tous les voyages à partir de l'ID trajet
     * 
     * @param id_trajet $id_trajet l'ID du trajet
     * @return Voyage[]|null
     */
    public static function getVoyagesByTrajetId($id_trajet) {
        $voyages = Voyage::find()->where(['trajet' => $id_trajet])->all();
        if($voyages) return $voyages;
        else return null;
    }

    /**
     * Récupère le voyage à partir de son ID
     * 
     * @param id $id l'ID du voyage
     * @return Voyage|null
     */
    public static function getVoyageById($id) {
        return Voyage::find()->where(['id' => $id])->one();
    }

    /**
     * Récupère tous les voyages qui correspondent à une recherche
     * 
     * @param id_trajet ID du trajet
     * @param nb_personnes Nombre de personnes
     */
    public static function getVoyagesByRecherche($id_trajet, $nb_personnes) {
        $voyages = Voyage::find()->where(['trajet' => $id_trajet])->all();
        if($voyages) return $voyages;
        else return null;
    }

    /**
     * Récupère tous les voyages qui correspondent à une recherche dans l'ordre des départs
     * 
     * @param id_trajet ID du trajet
     * @param nb_personnes Nombre de personnes
     */
    public static function getVoyagesByRechercheOrderByDate($id_trajet, $nb_personnes) {
        $voyages = Voyage::find()->where(['trajet' => $id_trajet])->orderBy(['heuredepart' => SORT_ASC])->all();
        if($voyages) return $voyages;
        else return null;
    }

    /**
     * Récupère tous les voyages qui correspondent à une recherche dans l'ordre des moins cher
     * 
     * @param id_trajet ID du trajet
     * @param nb_personnes Nombre de personnes
     */
    public static function getVoyagesByRechercheOrderByTarif($id_trajet, $nb_personnes) {
        $voyages = Voyage::find()->where(['trajet' => $id_trajet])->orderBy(['tarif' => SORT_ASC])->all();
        if($voyages) return $voyages;
        else return null;
    }

    /**
     * Vérifie la disponibilité d'un trajet
     * 
     * @param id ID du voyage
     * @param nb_personnes Nombre de personnes
     * @return true|false
     */
    public static function verifierDisponibilite($id, $nb_personnes) {
        $voyage = Voyage::getVoyageById($id);
        $reservations = Reservation::getReservationsByVoyageId($id);

        // J'avais oublié de gérer le nombre de réservations déjà effectuées car le seuil est nbplacedispo
        $nb_reservations = $nb_personnes;
        foreach($reservations as $reservation) $nb_reservations += $reservation->nbplaceresa;
        if($voyage->nbplacedispo >= $nb_reservations) return true;

        return false;
    }

    /**
     * Affiche les informations d'un voyage
     * 
     * @param Voyage    Instance de la classe Voyage
     * @param Recherche Recherche effectuée par l'utilisateur pour obtenir le voyage
     * @param string    Mode (public | conducteur) pour afficher soit le bouton "Réserver" soit les boutons "Modifier" et "Supprimer"
     */
    public static function afficherInformations($voyage, $recherche = null, $mode = 'public') {

        // Récupération du trajet correspondant au voyage
        $trajet = Trajet::findTrajetById($voyage->trajet);

        // Calcule la durée (1km = 1 min) du voyage
        $duree_minutes = Trajet::calculerDuree($trajet->distance);

        // Récupère l'instance du conducteur qui propose ce voyage
        $conducteur = User::findIdentity($voyage->conducteur);

        // Récupère l'instance de la voiture qui sera utilisée pour ce voyage
        $voiture = Voiture::getVoitureByIds($voyage->idtypev, $voyage->idmarquev);

        // Calcule le tarif par personne et le tarif total pour ce voyage
        $tarif_par_personne = $voyage->tarif * $trajet->distance;

        // Cas où l'utilisateur a effectué une recherche alors on lui affiche le tarif total selon le nombre de passagers qu'il a renseigné
        if($recherche !== null) $tarif_total = $tarif_par_personne * $recherche->nb_personnes;
        
        // Récupérations de toutes les réservations pour ce voyage
        $reservations = Reservation::getReservationsByVoyageId($voyage->id);
        $nb_reservations = 0;
        foreach($reservations as $reservation) $nb_reservations += $reservation->nbplaceresa;

        // Détermine le nombre de places restantes disponibles
        $nb_places_restantes = $voyage->nbplacedispo - $nb_reservations;

        // Vérifie la disponibilité du voyage
        if($recherche !== null && $mode === 'public') $available = Voyage::verifierDisponibilite($voyage->id, $recherche->nb_personnes);

        // Affichage de la ville de départ et d'arrivée
        echo Html::tag('h4', Html::encode($trajet->depart) . ' ➜ ' . Html::encode($trajet->arrivee), ['class' => 'card-title mb-3']);

        echo Html::beginTag('div', ['class' => 'row']);

            echo Html::beginTag('div', ['class' => 'col-md-6']);

                // Affichage de l'heure de départ
                echo Html::tag('p', '<strong>Heure de départ : </strong>' . Html::encode($voyage->heuredepart) . 'h', ['class' => 'mb-1']);

                // Affichage de la distance entre la ville de départ et la ville d'arrivée (en km)
                echo Html::tag('p', '<strong>Distance : </strong>' . $trajet->distance . ' km', ['class' => 'mb-1']);

                // Affichage du nombre de places maximum disponibles dans le véhicule
                echo Html::tag('p', '<strong>Nombre de places maximum disponibles dans le véhicule : </strong>' . Html::encode($voyage->nbplacedispo), ['class' => 'mb-1']);

                // Affichage du nombre de places réservées 
                echo Html::tag('p', '<strong>Nombre de places réservées : </strong>' . Html::encode($nb_reservations), ['class' => 'mb-1']);

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

                if($recherche !== null) {

                    // Affichage du prix par personne pour ce voyage 
                    if($recherche->nb_personnes > 1) echo Html::tag('p', '<strong>Tarif par personne : </strong>' . $tarif_par_personne . ' €', ['class' => 'mb-1']);

                    // Affichage du prix total pour ce voyage 
                    echo Html::tag('p', '<strong>Prix total pour ' . $recherche->nb_personnes . ' personnes : </strong>' . $tarif_total . ' €', ['class' => 'mb-1']);
                
                }

                // Si le voyage a des contraintes spécifiées par le conducteur
                if(!empty($voyage->contraintes)) echo Html::tag('p', '<strong>Contraintes : </strong>' . Html::encode($voyage->contraintes), ['class' => 'mb-1']);

            echo Html::endTag('div'); 

        echo Html::endTag('div'); 

        echo Html::beginTag('div', ['class' => 'mt-3 text-end']);

            // Affichage du voyage au grand public = possibilité de réserver le voyage
            if($mode === 'public') {

                // Si le voyage est disponible (le nombre de passagers entré par l'utilisateur est inférieur ou égal au nombre de places disponibles pour ce voyage)
                if($available) echo Html::button('Réserver', ['class' => 'btn btn-success reserver-voyage', 'data-id_voyage' => $voyage->id, 'data-nb_personnes' => $recherche->nb_personnes]);

                // Sinon affichage d'un bouton rouge non cliquable
                // On pense à la suite pour l'étape 5 qui devra implémenter la réservation d'un voyage
                // Dans ce cas le bouton n'est pas cliquable donc pas réservable
                else echo Html::button('Complet', ['class' => 'btn btn-danger', 'disabled' => true]);

            }

            // Affichage du voyage au conducteur = peut le modifier et le supprimer
            if($mode === 'conducteur') {

                // Affichage d'un bouton pour modifier la réservation
                echo Html::a('Modifier', ['site/modifier-voyage', 'id' => $voyage->id], ['class' => 'btn btn-warning me-2']);

                // Affichage d'un bouton pour supprimer la réservation
                echo Html::button('Supprimer', ['class' => 'btn btn-danger supprimer-voyage', 'data-id' => $voyage->id,]);

            }
        

        echo Html::endTag('div'); 
    }

    // modifierTarif($tarif)

    // modifierHeureDepart($heure_depart)

    // modifierNbBagagesParPersonne($nb_bagages_par_personne)

    // modifierNbPlacesTotal($nb_places)

    // ajouterContrainte($contrainte)

    // modifierContrainte($ancienne_contrainte, $nouvelle_contrainte)

    // supprimerContrainte($contrainte)

} 

?>