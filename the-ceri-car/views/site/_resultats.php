<?php

    use yii\helpers\Html;
    use app\models\Voyage;

    if($resultats) {    // Si au moins un voyage a été trouvé correspondant à la recherche

        // Respecter la contrainte de recherche d'un voyage de la veille pour le lendemain
        $tommorow = date('d/m/Y', strtotime('+1 day')); // Récupération de la date et l'incrémente d'1 jour
        $nb_voyages = count($resultats);    // Récupération du nombre de voyages trouvés

        // Affichage du nombre de voyages trouvés 
        // Désormais géré par le bandeau de notification
        //echo Html::tag('h3', $nb_voyages . ' voyage' . ($nb_voyages > 1 ? 's' : '') . ' disponible' . ($nb_voyages > 1 ? 's' : '') . ' pour demain (' . $tommorow . ')', ['class' => 'mb-4 text-center']);

        // Affichage des voyages trouvés
        foreach ($resultats as $resultat) {

            echo Html::beginTag('div', ['class' => 'card voyage-card shadow-sm mb-4']);
                echo Html::beginTag('div', ['class' => 'card-body']);

                    if($resultat['type'] === 'direct') Voyage::afficherInformations($resultat['voyage'], $recherche);   // Affichage d'un voyage
                    else Voyage::afficherInformationsCorrespondance($resultat['voyages'], $resultat['ville_correspondance'], $recherche);   // Affichage d'une correspondance

                echo Html::endTag('div');
            echo Html::endTag('div');
        }

    }
            
    // Cas où aucun trajet n'a été trouvé
    // On n'affiche plus car désormais on a le bandeau de notification
    //else echo Html::tag('div', 'Aucun trajet disponible pour cette recherche.', ['class' => 'alert alert-warning']);