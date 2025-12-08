<?php 

namespace app\models; 

class Voiture extends \yii\base\BaseObject { 

    public $idType;
    public $type;
    public $idMarque;
    public $marque;

    /**
     * Gets voiture by both ids
     * 
     * @param idType $id the type id
     * @param idMarque $id the brand id
     * @return static|null
     */
    public static function getVoitureByIds($idType, $idMarque) {

        // Récupération des instances des deux classes des deux tables
        $type = TypeVehicule::findTypeById($idType);
        $marque = MarqueVehicule::findBrandById($idMarque);

        if($type && $marque) {
            return new static([
                'idType' => $idType,
                'type' => $type->typev,
                'idMarque' => $idMarque,
                'marque' => $marque->marquev,
            ]);
        }
        else return null;

    }

    // afficherInformations($id)

    // modifierType($id, $type)

    // modifierMarque($id, $marque)

} 

?>