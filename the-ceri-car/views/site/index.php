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
            'method' => 'get',  // méthode GET pour le formulaire
            'options' => ['class' => 'search-form d-flex flex-wrap gap-3 justify-content-center'], // <form class="..."></form>
            'fieldConfig' => [
                'template' => "{input}\n{error}",   // input puis les erreurs en dessous si elles sont détectées
                'errorOptions' => ['class' => 'mt-3 text-danger small'],
                'inputOptions' => ['class' => 'form-control'],
            ],
        ]);

        // Champ pour la ville de départ
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'ville_depart')->textInput(['placeholder' => 'Ville de départ', 'id' => 'ville_depart']);
        echo Html::endTag('div');

        // Champ pour la ville d'arrivée
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'ville_arrivee')->textInput(['placeholder' => 'Ville d’arrivée', 'id' => 'ville_arrivee']);
        echo Html::endTag('div');

        // Champ pour le nombre de passagers
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo $form->field($recherche, 'nb_personnes')->input('number', ['min' => 1, 'max' => 10, 'placeholder' => 'Nombre de passagers', 'id' => 'nb_personnes']);
        echo Html::endTag('div');

        // Bouton pour lancer la recherche
        echo Html::beginTag('div', ['class' => 'flex-field']);
            echo Html::submitButton('Rechercher', ['id' => 'btn-search', 'class' => 'btn btn-custom px-4 w-100']);
        echo Html::endTag('div');

        ActiveForm::end(); 

    echo Html::endTag('div');
    
    // Cas où le formulaire de recherche a bien été soumis
    if(!empty($_GET['RechercheForm'])) {

        echo Html::beginTag('div', ['class' => 'contrainer results-section', 'id' => 'results']);

            // Si au moins un voyage a été trouvé correspondant à la recherche
            if($resultats) {

                // Respecter la contrainte de recherche d'un voyage de la veille pour le lendemain
                $tommorow = date('d/m/Y', strtotime('+1 day')); // récupération de la date et +1 jour
                // Récupération du nombre de voyages trouvés
                $nb_voyages = count($resultats);

                // Affichage du nombre de voyages trouvés
                echo Html::tag('h3', $nb_voyages . ' voyage' . ($nb_voyages > 1 ? 's' : '') . ' disponible' . ($nb_voyages > 1 ? 's' : '') . ' pour demain (' . $tommorow . ')', ['class' => 'mb-4']);

                // Affichage des voyages trouvés
                foreach($resultats as $voyage) {
                    echo Html::beginTag('div', ['class' => 'card voyage-card shadow-sm mb-4']);
                        echo Html::beginTag('div', ['class' => 'card-body']);

                            // Affiche de la card avec les informations du voyage
                            Voyage::afficherInformations($voyage, $recherche);

                        echo Html::endTag('div'); 
                    echo Html::endTag('div'); 
                }

            }
            
            // Cas où aucun trajet n'a été trouvé
            else echo Html::tag('div', 'Aucun trajet disponible pour cette recherche.', ['class' => 'alert alert-warning']);

            echo Html::endTag('div');

        }

echo Html::endTag('div');

?>
