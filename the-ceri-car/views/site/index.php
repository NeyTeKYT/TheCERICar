<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $recherche */

use yii\bootstrap5\ActiveForm;
use app\models\Voyage;
use yii\helpers\Html;

$this->title = 'Rechercher un voyage';
/*$this->params['breadcrumbs'][] = $this->title;*/  // affiche le chemin dans l'arborescence du site

echo Html::beginTag('div', ['class' => 'site-index']);

    echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);

        // Affichage du titre de la page avec un slogan
        echo Html::tag('h1', Html::encode($this->title));
        echo Html::tag('p', 'Trouvez facilement un trajet selon vos besoins.', ['class' => 'lead']);

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'container mb-5']);

        // Création d'un formulaire en utilisant la classe RechercheForm
        $form = ActiveForm::begin([
            'id' => 'recherche-form',   // <form id='recherche-form'></form>
            //'method' => 'get',  // méthode GET pour le formulaire
            'method' => 'get',
            'options' => ['class' => 'search-form d-flex flex-wrap gap-3 justify-content-center'], // <form class="..."></form>
            'fieldConfig' => [
                'template' => "{input}\n{error}",   // input puis les erreurs en dessous si elles sont détectées
                'errorOptions' => ['class' => 'mt-3 text-danger small'],
                'inputOptions' => ['class' => 'form-control'],
            ],
        ]);

        // Champ pour la ville de départ
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'ville_depart')->textInput(['placeholder' => 'Ville de départ']);
        echo Html::endTag('div');

        // Champ pour la ville d'arrivée
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'ville_arrivee')->textInput(['placeholder' => 'Ville d’arrivée']);
        echo Html::endTag('div');

        // Champ pour le nombre de passagers
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'nb_personnes')->input('number', ['min' => 1, 'max' => 10, 'placeholder' => 'Nombre de passagers']);
        echo Html::endTag('div');

        // Bouton pour lancer la recherche
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo Html::submitButton('Rechercher', ['id' => 'btn-search', 'class' => 'btn btn-custom px-4 w-100']);
        echo Html::endTag('div');

        ActiveForm::end(); 

    echo Html::endTag('div');

    // Div pour l'affichage des résultats
    echo Html::beginTag('div', ['class' => 'container results-section', 'id' => 'resultats']);
    echo Html::endTag('div');

echo Html::endTag('div');

?>
