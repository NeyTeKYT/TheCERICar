<?php 

namespace app\models; 

use yii\db\ActiveRecord; 

class TypeVehicule extends ActiveRecord { 

    public static function tableName() { 
        return 'fredouil.typevehicule'; 
    } 

    /**
     * Finds vehicule type by id
     * 
     * @param id $id the type vehicule id
     * @return static|null
     */
    public static function findTypeById($id) {
        $type_vehicule = TypeVehicule::find()->where(['id' => $id])->one();
        if($type_vehicule) return $type_vehicule;
        else return null;
    }

} 

?>