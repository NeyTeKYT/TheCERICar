<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RechercheForm is the model behind the recherche form.
 */
class RechercheForm extends Model {

    public $nb_personnes;
    public $correspondances;    // boolean
    public $ville_depart;
    public $ville_arrivee;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [

            // Les champs obligatoires
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
    public static function lancerRecherche($nb_personnes, $correspondances, $ville_depart, $ville_arrivee) {

        // Récupère l'instance Trajet correspondante
        $trajet = Trajet::getTrajet($ville_depart, $ville_arrivee);
        if(!$trajet) return null;

        // Récupère les instances de la classe Voyage qui correspondent à la recherche
        return Voyage::getVoyagesByRecherche($trajet->id, $nb_personnes);
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
