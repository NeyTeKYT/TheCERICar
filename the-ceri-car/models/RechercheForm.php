<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RechercheForm est le model derrière le formulaire de recherche.
 */
class RechercheForm extends Model {

    // Données entrées par l'utilisateur pour rechercher un voyage
    public $nb_personnes;
    public $avec_correspondances = false;    // Par défaut à false, par défaut on ne cherche que les voyages avec des trajets directs.
    public $ville_depart;
    public $ville_arrivee;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [

            // Attributs obligatoires pour rechercher u voyage
            ['nb_personnes', 'required', 'message' => 'Le nombre de passagers doit être renseigné.'],
            ['ville_depart', 'required', 'message' => 'La ville de départ doit être renseignée.'],
            ['ville_arrivee', 'required', 'message' => "La ville d'arrivée doit être renseignée."],

            // Format des villes qui commencent par une majuscule puis se termine par des lettres minuscules et certains caractères spéciaux autorisés
            ['ville_depart', 'match', 'pattern' => '/^[A-Z][a-z\' -]+$/', 'message' => 'La ville de départ doit commencer par une majuscule et ne doit contenir que des lettres.'],
            ['ville_arrivee', 'match', 'pattern' => '/^[A-Z][a-z\' -]+$/', 'message' => "La ville d'arrivée doit commencer par une majuscule et ne doit contenir que des lettres."],

            // La longueur maximum pour la ville de départ et d'arrivée doit être de 45 caractères
            [['ville_depart', 'ville_arrivee'], 'string', 'max' => 45, 'tooLong' => 'Le nom de la ville ne peut pas dépasser 45 caractères.'],

            // La ville de départ et la ville d'arrivée doivent être différentes
            ['ville_arrivee', 'compare', 'compareAttribute' => 'ville_depart', 'operator' => '!=', 'message' => "La ville de départ et d'arrivée doivent être différentes."],

            // Nombre de personnes : entier entre 1 et 10
            ['nb_personnes', 'integer', 'min' => 1, 'max' => 10, 
            'tooSmall' => 'Le nombre de personnes doit être au minimum 1.', 
            'tooBig' => 'Le nombre de personnes ne peut pas dépasser 10.',
            'message' => 'Le nombre de personnes doit être un nombre entier.'
            ],

            // Checkbox pour accepter ou non les correspondances qui fonctionnent avec la recherche de l'utilisateur
            ['avec_correspondances', 'default', 'value' => 0],
            ['avec_correspondances', 'in', 'range' => [0, 1]],

        ];
    }

    /**
     * Lance une recherche à partir des informations 
     * 
     * @param int Nombre de personnes
     * @param bool Si l'utilisateur accepte des voyages avec correspondance
     * @param string Ville de départ du voyage
     * @param string Ville d'arrivée du voyage
     * @return Voyage[]|null
     */
    public static function lancerRecherche($nb_personnes, $avec_correspondances, $ville_depart, $ville_arrivee) {

        // Recherche de voyages avec trajets directs
        if(!$avec_correspondances) {
            // Récupère l'instance Trajet correspondante
            $trajet = Trajet::getTrajet($ville_depart, $ville_arrivee);
            if(!$trajet) return null;

            // Récupère les instances de la classe Voyage qui correspondent à la recherche
            $voyages = Voyage::getVoyagesByRecherche($trajet->id, $nb_personnes);
            if(!$voyages) return null;

            $resultats = [];
            foreach ($voyages as $voyage) {
                $resultats[] = [
                    'type' => 'direct',
                    'voyage' => $voyage
                ];
            }
            return $resultats;

        }

        $resultats = [];    // Déclare un tableau de resultats vide pour le moment

        // Récupère tous les trajets qui partent de la ville de départ souhaitée par l'utilisateur
        $trajetsDepart = Trajet::find()->where(['depart' => $ville_depart])->all();

        foreach($trajetsDepart as $t1) {

            // Récupère tous les trajets qui partent de la ville d'arrivée des trajets récupérées et s'arrêtent à la ville d'arrivée souhaitée
            $trajetsArrivee = Trajet::find()->where(['depart' => $t1->arrivee, 'arrivee' => $ville_arrivee])->all();

            foreach($trajetsArrivee as $t2) {

                // Création d'instances pour les trajets
                $voyages1 = Voyage::getVoyagesByTrajetId($t1->id);
                $voyages2 = Voyage::getVoyagesByTrajetId($t2->id);

                Yii::debug([
                            'trajetsDepart' => count($trajetsDepart),
                        ], 'DEBUG');

                        Yii::debug([
                            'trajetsArrivee' => count($trajetsArrivee),
                        ], 'DEBUG');

                        Yii::debug([
                            'voyages1' => count($voyages1),
                            'voyages2' => count($voyages2),
                        ], 'DEBUG');

                if(empty($voyages1) || empty($voyages2)) continue;

                foreach($voyages1 as $v1) {
                    foreach($voyages2 as $v2) {

                        $departV1 = $v1->heuredepart * 60;
                        $departV2 = $v2->heuredepart * 60;

                        // Détermine la durée totale de la correspondance
                        $duree1 = Trajet::calculerDuree($t1->distance);
                        $duree2 = Trajet::calculerDuree($t2->distance);

                        // Vérifie la compatibilité de l'heure d'arrivée du voyage 1 avec l'heure de départ du voyage 2 
                        $heureArriveeV1 = $departV1 + $duree1;
                        if($heureArriveeV1 >= $departV2) continue;

                        // Empêche une durée de correspondances > à 1440 minutes = 24 heures car sinon on va boucler
                        $total = ($departV2 - $departV1) + $duree2;
                        if($total <= 0 || $total > 1440) continue;

                        // Vérifie la disponibilité dans tous les voyages
                        if(!Voyage::verifierDisponibilite($v1->id, $nb_personnes)) continue;
                        if(!Voyage::verifierDisponibilite($v2->id, $nb_personnes)) continue;

                        $resultats[] = [
                            'type' => 'correspondance',
                            'voyages' => [$v1, $v2],
                            'ville_correspondance' => $t1->arrivee
                        ];

                    }
                }
            }          
        }

        return $resultats;

    }

    /**
     * Lance une recherche à partir des informations DANS L'ORDRE DE L'HEURE DE DÉPART
     * 
     * @param nb_personnes Nombre de personnes
     * @param correspondances Si l'utilisateur accepte des voyages avec correspondance
     * @param ville_depart Ville de départ du voyage
     * @param ville_arrivee Ville d'arrivée du voyage
     * @return Voyage[]|null
     */
    public static function lancerRechercheOrderByDate($nb_personnes, $correspondances, $ville_depart, $ville_arrivee) {

        // Récupère l'instance Trajet correspondante
        $trajet = Trajet::getTrajet($ville_depart, $ville_arrivee);
        if(!$trajet) return null;

        // Récupère les instances de la classe Voyage qui correspondent à la recherche
        return Voyage::getVoyagesByRechercheOrderByDate($trajet->id, $nb_personnes);
    }

    /**
     * Lance une recherche à partir des informations DANS L'ORDRE DES COÛTS DE TRAJET
     * 
     * @param nb_personnes Nombre de personnes
     * @param correspondances Si l'utilisateur accepte des voyages avec correspondance
     * @param ville_depart Ville de départ du voyage
     * @param ville_arrivee Ville d'arrivée du voyage
     * @return Voyage[]|null
     */
    public static function lancerRechercheOrderByTarif($nb_personnes, $correspondances, $ville_depart, $ville_arrivee) {

        // Récupère l'instance Trajet correspondante
        $trajet = Trajet::getTrajet($ville_depart, $ville_arrivee);
        if(!$trajet) return null;

        // Récupère les instances de la classe Voyage qui correspondent à la recherche
        return Voyage::getVoyagesByRechercheOrderByTarif($trajet->id, $nb_personnes);
    }

}
