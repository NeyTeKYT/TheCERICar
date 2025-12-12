<?php

    use yii\helpers\Html;
    use app\models\Voyage;

    // Si au moins un voyage a été trouvé correspondant à la recherche
    if($resultats) {

        // Respecter la contrainte de recherche d'un voyage de la veille pour le lendemain
        $tommorow = date('d/m/Y', strtotime('+1 day')); // récupération de la date et +1 jour
        // Récupération du nombre de voyages trouvés
        $nb_voyages = count($resultats);

        // Affichage du nombre de voyages trouvés
        //echo Html::tag('h3', $nb_voyages . ' voyage' . ($nb_voyages > 1 ? 's' : '') . ' disponible' . ($nb_voyages > 1 ? 's' : '') . ' pour demain (' . $tommorow . ')', ['class' => 'mb-4 text-center']);

        // Affichage des voyages trouvés
        foreach($resultats as $voyage) {
            if($recherche->nb_personnes <= $voyage->nbplacedispo) {
                echo Html::beginTag('div', ['class' => 'card voyage-card shadow-sm mb-4']);
                    echo Html::beginTag('div', ['class' => 'card-body']);

                        // Affiche de la card avec les informations du voyage
                        Voyage::afficherInformations($voyage, $recherche);

                    echo Html::endTag('div'); 
                echo Html::endTag('div'); 
            }
        }

    }
            
    // Cas où aucun trajet n'a été trouvé
    // On n'affiche plus car désormais on a le bandeau de notification
    //else echo Html::tag('div', 'Aucun trajet disponible pour cette recherche.', ['class' => 'alert alert-warning']);