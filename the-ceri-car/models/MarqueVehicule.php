<?php 

namespace app\models; 

use yii\db\ActiveRecord; 

class MarqueVehicule extends ActiveRecord { 

    public static function tableName() { 
        return 'fredouil.marquevehicule'; 
    } 

    /**
     * Finds vehicule brand by id
     * 
     * @param id $id the vehicule brand id
     * @return static|null
     */
    public static function findBrandById($id) {
        $marque_vehicule = MarqueVehicule::find()->where(['id' => $id])->one();
        if($marque_vehicule) return $marque_vehicule;
        else return null;
    }

} 

?>