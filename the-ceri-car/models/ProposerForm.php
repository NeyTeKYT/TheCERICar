<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ProposerForm is the model behind the "Proposer un voyage" form.
 */
class ProposerForm extends Model {

    public $trajet; // Il s'agit de l'ID du trajet
    public $heuredepart;
    public $nbplacedispo;
    public $tarif;
    public $nbbagage;
    public $contraintes;
    public $idtypev;
    public $idmarquev;
    public $voyage_id;

    /**
     * @return array the validation rules
     */
    public function rules() {
        return [

            // Les champs obligatoires
            ['trajet', 'required', 'message' => 'Le trajet du voyage doit être renseigné.'],
            ['heuredepart', 'required', 'message' => "L'heure de départ du voyage doit être renseigné"],
            ['nbplacedispo', 'required', 'message' => "Le nombre de places maximum disponibles doit être renseigné."],
            ['tarif', 'required', 'message' => 'Le tarif par personne par kilomètre doit être renseigné.'],
            ['nbbagage', 'required', 'message' => 'Le nombre de bagages par personne doit être renseigné.'],
            ['idtypev', 'required', 'message' => "Le type de véhicule doit être renseigné."],
            ['idmarquev', 'required', 'message' => "La marque du véhicule doit être renseignée"],

            // Le trajet est un ID, il doit donc être un integer car on récupère l'ID en fonction de la ville de départ et d'arrivée
            ['trajet', 'integer'],

            // L'heure de départ doit être un entier 
            ['heuredepart', 'integer', 'min' => 0, 'max' => 23,
                'tooSmall' => 'L’heure doit être comprise entre 0 et 23.',
                'tooBig' => 'L’heure doit être comprise entre 0 et 23.',
                'message' => "L'heure de départ doit être un nombre entier."
            ],

            // Le nombre de places maximum disponibles doit être un entier
            ['nbplacedispo', 'integer', 'min' => 1,
                'tooSmall' => 'Il doit y avoir au moins 1 place.',
                'message' => "Le nombre de places maximum disponibles doit être un nombre entier."
            ],
            ['nbplacedispo', 'validateNbPlaces'],

            // Le tarif par personne par kilomètre doit être un nombre entier supérieur à 0
            ['tarif', 'number', 'min' => 0.1,
                'tooSmall' => 'Le tarif doit être supérieur à 0.',
                'message' => 'Le tarif doit être un nombre entier.'
            ],

            // Bagages par personne
            ['nbbagage', 'integer', 'min' => 0,
                'tooSmall' => 'Le nombre de bagages par personne doit être au minimum 0.',
                'message' => 'Le nombre de bagages par personne doit être un nombre entier'
            ],

            // Les contraintes sont facultatives
            ['contraintes', 'string'],

            // Le type de véhicule doit exister dans la table typevehicule.
            [['idtypev'], 'exist',
                'targetClass' => TypeVehicule::class,
                'targetAttribute' => 'id'
            ],

            // La marque du véhicule doit exister dans la table marquevehicule.
            [['idmarquev'], 'exist',
                'targetClass' => MarqueVehicule::class,
                'targetAttribute' => 'id'
            ],

            ['voyage_id', 'integer'],
        ];
    }

    /**
     * Ajoute un voyage proposé par un conducteur dans la base de données.
     *
     * @param int $conducteurId
     * @return bool
     */
    public function proposerVoyage($conducteurId) {

        // Cas d'arrêt
        if(!$this->validate()) return false;

        // Création d'une instance de la table Voyage
        $voyage = new Voyage();
        $voyage->conducteur = $conducteurId;
        $voyage->trajet = $this->trajet;
        $voyage->heuredepart = $this->heuredepart;
        $voyage->nbplacedispo = $this->nbplacedispo;
        $voyage->tarif = $this->tarif;
        $voyage->nbbagage = $this->nbbagage;
        $voyage->contraintes = $this->contraintes;
        $voyage->idtypev = $this->idtypev;
        $voyage->idmarquev = $this->idmarquev;

        return $voyage->save();
    }

    /**
     * Modifie un voyage proposé par un conducteur dans la base de données.
     * 
     * @param Voyage Instance du voyage à modifier
     */
    public function loadFromVoyage(Voyage $voyage) {
        $this->trajet = $voyage->trajet;
        $this->heuredepart = $voyage->heuredepart;
        $this->idtypev = $voyage->idtypev;
        $this->idmarquev = $voyage->idmarquev;
        $this->nbplacedispo = $voyage->nbplacedispo;
        $this->tarif = $voyage->tarif;
        $this->nbbagage = $voyage->nbbagage;
        $this->contraintes = $voyage->contraintes;
    }

    /**
     * Modifie un voyage proposé par un conducteur dans la base de données.
     * 
     */
    public function updateVoyage(Voyage $voyage) {

        // Cas d'arrêt
        if(!$this->validate()) return false;

        //$voyage->trajet = $this->trajet;  // Le trajet ne doit pas être modifiable !
        $voyage->heuredepart = $this->heuredepart;
        $voyage->nbplacedispo = $this->nbplacedispo;
        $voyage->tarif = $this->tarif;
        $voyage->nbbagage = $this->nbbagage;
        $voyage->contraintes = $this->contraintes;
        $voyage->idtypev = $this->idtypev;
        $voyage->idmarquev = $this->idmarquev;

        return $voyage->save(false);
    }

    /**
     * Vérifie que le nombre de places est possible dans le cas d'une modification en fonction du nombre de places déjà réservées.
     * 
     * @param integer attribute, valeur du champ "Nombre de places maximum disponibles"
     */
    public function validateNbPlaces($attribute) {

        if(!$this->voyage_id) return;   // Création d'un voyage donc OK

        // Récupération du voyage
        $voyage = Voyage::getVoyageById($this->voyage_id);
        // Récupération de toutes les réservations associées à ce voyage
        $reservations = Reservation::getReservationsByVoyageId($voyage->id);

        $nb_places_reservees = 0;
        foreach($reservations as $reservation) $nb_places_reservees += $reservation->nbplaceresa;

        if($this->$attribute < $nb_places_reservees) $this->addError($attribute, "Impossible de réduire le nombre de places en dessous des $nb_places_reservees places déjà réservées.");

    }

}
