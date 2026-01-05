<?php

use yii\helpers\Html;
use app\models\Reservation;

$this->title = 'Mes réservations';

echo Html::beginTag('div');

    echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);  

        // Affichage du titre de la page avec une description
        echo Html::tag('h1', Html::encode($this->title));
        echo Html::tag('p', Html::encode("Gérez vos réservations effectuées."), ['class' => 'lead']);

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'container mt-5']);

        if($reservations) {
            foreach ($reservations as $reservation) {

                echo Html::beginTag('div', ['class' => 'card voyage-card shadow-sm mb-4']);
                    echo Html::beginTag('div', ['class' => 'card-body']);

                        // Affichage des infos de la réservation
                        Reservation::afficherInformations($reservation);

                    echo Html::endTag('div');
                echo Html::endTag('div');
            }
        } 
        
        else echo Html::tag('div', "Vous n'avez pas encore effectué de réservation.", ['class' => 'alert alert-info text-center']);

    echo Html::endTag('div');

echo Html::endTag('div');

?>