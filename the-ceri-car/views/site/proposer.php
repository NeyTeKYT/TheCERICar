<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use app\models\Trajet;
    use app\models\TypeVehicule;
    use app\models\MarqueVehicule;

    // Titre de la page dans l'onglet
    $this->title = 'Proposer un voyage';

    echo Html::beginTag('div');

        echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);

            // Affichage du titre de la page avec un slogan
            echo Html::tag('h1', Html::encode($this->title));
            echo Html::tag('p', 'Proposez un voyages empruntables par les autres utilisateurs.', ['class' => 'lead']);

        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'container mb-5']);

            // Création d'un formulaire en utilisant la classe ProposerVoyageForm
            $form = ActiveForm::begin([
                'id' => 'proposer-form',    // <form id='proposer-form'>
                'method' => 'post',
                'options' => ['class' => 'search-form d-flex flex-column flex-wrap gap-3 justify-content-center'], // <form class="...">
                'fieldConfig' => [
                    'template' => "{input}\n{error}",   // input puis les erreurs en dessous si elles sont détectées
                    'errorOptions' => ['class' => 'mt-3 text-danger small'],
                    'inputOptions' => ['class' => 'form-control'],
                ],
            ]);

            // Champ pour sélectionner le trajet parmi tous les trajets disponibles dans la table de la BDD
            echo $form->field($model, 'trajet')->dropDownList(ArrayHelper::map(
                Trajet::find()->all(), 'id', 
                function($trajet) {
                    return $trajet->depart . ' → ' . $trajet->arrivee;
                }
            ), ['prompt' => 'Choisir un trajet', 'autofocus' => true]);

            // Champ pour l'heure de départ
            echo $form->field($model, 'heuredepart')->input('number', ['placeholder' => "Heure de départ"]);  // L'heure doit être un nombre entier

            // Champ pour choisir le type de véhicule
            echo $form->field($model, 'idtypev')->dropDownList(
                ArrayHelper::map(TypeVehicule::find()->all(), 'id', 'typev'),
                ['prompt' => 'Type de véhicule']
            );

            // Champ pour choisir la marque du véhicule
            echo $form->field($model, 'idmarquev')->dropDownList(
                ArrayHelper::map(MarqueVehicule::find()->all(), 'id', 'marquev'),
                ['prompt' => 'Marque du véhicule']
            );

            // Champ pour le nombre de places maximum disponibles
            echo $form->field($model, 'nbplacedispo')->input('number', ["placeholder" => "Nombre de places maximum disponibles"]);
    
            // Champ pour le tarif du voyage par kilomètre par personne
            echo $form->field($model, 'tarif')->input('number', ['step' => '0.01', "placeholder" => "Tarif par kilomètre par personne"]);
    
            // Champ pour le nombre de bagages maximum par personne
            echo $form->field($model, 'nbbagage')->input('number', ["min" => 1, "placeholder" => "Nombre de bagages maximum par personne"]);
    
            // Champ pour ajouter des contraintes
            echo $form->field($model, 'contraintes')->textarea(['placeholder' => "Contraintes"]);

            // Bouton pour proposer le voyage
            echo Html::submitButton('Proposer le voyage', ['id' => 'btn-search', 'class' => 'btn btn-custom px-4 w-100']);

            ActiveForm::end();

        echo Html::endTag('div');

    echo Html::endTag('div');

?>
