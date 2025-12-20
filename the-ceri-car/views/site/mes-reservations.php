<?php

    use yii\helpers\Html;
    use app\models\Reservation;

    $this->title = 'Mes réservations';

    echo Html::beginTag('div', ['class' => 'container mt-5']);

        echo Html::tag('h2', 'Mes réservations', ['class' => 'mb-4 text-center']);

        if ($reservations) {
            foreach ($reservations as $reservation) {

                echo Html::beginTag('div', ['class' => 'card voyage-card shadow-sm mb-4']);
                    echo Html::beginTag('div', ['class' => 'card-body']);

                        // Affichage des infos de la réservation
                        Reservation::afficherInformations($reservation);

                    echo Html::endTag('div');
                echo Html::endTag('div');
            }
        } else {
            echo Html::tag(
                'div',
                "Vous n'avez encore effectué aucune réservation.",
                ['class' => 'alert alert-info text-center']
            );
        }

    echo Html::endTag('div');

?>