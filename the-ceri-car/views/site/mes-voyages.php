<?php

    use yii\helpers\Html;
    use app\models\Voyage;

    $this->title = 'Mes voyages';

    echo Html::beginTag('div', ['class' => 'container mt-5']);

        echo Html::tag('h2', 'Mes voyages proposés', ['class' => 'mb-4 text-center']);

        if ($voyages) {
            foreach ($voyages as $voyage) {

                echo Html::beginTag('div', ['class' => 'card voyage-card shadow-sm mb-4']);
                    echo Html::beginTag('div', ['class' => 'card-body']);

                        // Réutilisation intelligente de ton affichage existant
                        // Ici on simule une "recherche" avec 1 personne pour l’affichage
                        $fakeRecherche = (object) ['nb_personnes' => 1];
                        Voyage::afficherInformations($voyage, $fakeRecherche);

                    echo Html::endTag('div');
                echo Html::endTag('div');
            }
        } else {
            echo Html::tag(
                'div',
                "Vous n'avez encore proposé aucun voyage.",
                ['class' => 'alert alert-info text-center']
            );
        }

    echo Html::endTag('div');

?>
