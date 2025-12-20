<?php

    use yii\helpers\Html;
    use app\models\Voyage;

    $this->title = 'Mes voyages';

    echo Html::beginTag('div', ['class' => 'container mt-5']);

        echo Html::tag('h2', 'Mes voyages proposés', ['class' => 'mb-4 text-center']);

        if($voyages) {
            foreach($voyages as $voyage) {

                echo Html::beginTag('div', ['class' => 'card voyage-card shadow-sm mb-4']);
                    echo Html::beginTag('div', ['class' => 'card-body']);

                        // Affichage des voyages version conducteur = peut modifier et supprimer un voyage
                        Voyage::afficherInformations($voyage, null, 'conducteur');

                    echo Html::endTag('div');
                echo Html::endTag('div');
            }
        } 
        
        else echo Html::tag('div', "Vous ne proposez pas encore de voyage sur TheCeriCar.", ['class' => 'alert alert-info text-center']);

    echo Html::endTag('div');

?>
