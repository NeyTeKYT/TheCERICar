<?php

use yii\bootstrap5\ActiveForm;
use app\models\Voyage;
use yii\helpers\Html;

$this->title = 'Rechercher un voyage';

echo Html::beginTag('div');

    echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);

        // Affichage du titre de la page avec un slogan
        echo Html::tag('h1', Html::encode($this->title));
        echo Html::tag('p', Html::encode('Trouvez facilement un trajet selon vos besoins.'), ['class' => 'lead']);

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'container mb-5']);

        // Création d'un formulaire en utilisant la classe RechercheForm
        $form = ActiveForm::begin([
            'id' => 'recherche-form',   // <form id='recherche-form'>
            'method' => 'get',  // Méthode GET car les données ne sont pas sensibles
            'options' => ['class' => 'form d-flex flex-wrap gap-3 justify-content-center'], // <form class="..."></form>
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'form-label fw-semibold mb-1'],
                'inputOptions' => ['class' => 'form-control'],
                'errorOptions' => ['class' => 'invalid-feedback d-block'],
            ],
        ]);

        // Champ pour la ville de départ
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'ville_depart')->textInput(['autofocus' => true, 'placeholder' => 'Ville de départ'])->label("Ville de départ");
        echo Html::endTag('div');

        // Champ pour la ville d'arrivée
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'ville_arrivee')->textInput(['placeholder' => 'Ville d’arrivée'])->label("Ville d'arrivée");
        echo Html::endTag('div');

        // Champ pour le nombre de passagers
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'nb_personnes')->input('number', ['min' => 1, 'max' => 10, 'placeholder' => 'Nombre de passagers'])->label("Nombre de passagers");
        echo Html::endTag('div');

        // Champ pour rechercher des correspondances
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo Html::tag('label', '&nbsp;', ['class' => 'form-label fw-semibold mb-1']);  // Label à la checkbox pour la mettre à la même hauteur que les champs
            echo $form->field($recherche, 'avec_correspondances')->checkbox(['value' => 1, 'uncheck' => 0])->label("Accepter les voyages avec correspondances");
        echo Html::endTag('div');

        // Bouton pour lancer la recherche
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo Html::tag('label', '&nbsp;', ['class' => 'form-label fw-semibold mb-1']);  // Label au bouton pour le mettre à la même hauteur que les champs
            echo Html::submitButton('Rechercher', ['id' => 'btn-search', 'class' => 'btn btn-custom px-4 w-100']);
        echo Html::endTag('div');

        ActiveForm::end(); 

    echo Html::endTag('div');

    // Div pour l'affichage des résultats
    echo Html::beginTag('div', ['class' => 'container results-section', 'id' => 'resultats']);
    echo Html::endTag('div');

echo Html::endTag('div');

?>
