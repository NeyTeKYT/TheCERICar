<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Reservation;
use app\models\Voyage;

$this->title = 'Modifier ma réservation';

echo Html::beginTag('div');

    echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);

        // Affichage du titre de la page avec une description
        echo Html::tag('h1', Html::encode($this->title));
        echo Html::tag('p', Html::encode("Modifiez le nombre de places réservées."), ['class' => 'lead']);

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'container mb-5']);

        if($reservation) {

            $form = ActiveForm::begin([
                'id' => 'modifier-reservation-form',    // <form id='modifier-reservation-form'></form>
                'action' => ['site/modifier-reservation', 'id' => $reservation->id],
                'method' => 'post', // Méthode POST pour plus de sécurité lors de l'envoi des données
                'options' => ['class' => 'form d-flex flex-column gap-3 justify-content-center'],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'form-label fw-semibold mb-1'],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]);

            // Récupération du voyage correspondant
            $voyage = Voyage::getVoyageById($reservation->voyage);

            // Récupérations de toutes les réservations pour ce voyage
            $reservations = Reservation::getReservationsByVoyageId($voyage->id);
            $nb_places_reservees = 0;
            foreach($reservations as $r) $nb_places_reservees += $r->nbplaceresa;

            // Détermine le nombre de places restantes disponibles
            $nb_places_restantes = $voyage->nbplacedispo - $reservation->nbplaceresa;

            // Détermine le nombre de places réservables au maximum
            $nb_places_reservables_max = $nb_places_restantes + $reservation->nbplaceresa;

            // Champ nombre de places
            echo $form->field($reservation, 'nbplaceresa')->input('number', ['autofocus' => true, 'min' => 1, 'max' => $nb_places_reservables_max])->label('Nombre de places réservées');
            
            echo Html::beginTag('div', ['class' => 'form-group']);
                echo Html::beginTag('div', ['class' => 'd-flex flex-column gap-2 mt-4']);

                    // Bouton pour modifier le nombre de places de la réservation
                    echo Html::submitButton('Modifier le nombre de places réservées', ['class' => 'btn btn-custom']);

                    // Revenir à la page des réservations
                    echo Html::a('Revenir à la liste de vos réservations', ['site/mes-reservations'], ['class' => 'btn btn-danger']);

                echo Html::endTag('div');
            echo Html::endTag('div');

            ActiveForm::end();

        }

        else echo Html::tag('div', "Cette réservation est indisponible.", ['class' => 'alert alert-info text-center']);

    echo Html::endTag('div');

echo Html::endTag('div');

?>