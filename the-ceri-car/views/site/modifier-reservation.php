<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Modifier la réservation';

?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Modifier ma réservation</h2>

    <?php $form = ActiveForm::begin([
        'id' => 'modifier-reservation-form',
        'action' => '/site/modifier-reservation?id=' . $reservation->id,
    ]); 
?>


        <?= $form->field($reservation, 'nbplaceresa')
            ->input('number', [
                'min' => 1,
                'class' => 'form-control',
            ])
            ->label('Nombre de places réservées') ?>

        <div class="mt-3 text-end">
            <?= Html::submitButton('Enregistrer', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Annuler', ['site/mes-reservations'], ['class' => 'btn btn-secondary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
