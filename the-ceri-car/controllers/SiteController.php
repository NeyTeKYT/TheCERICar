<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\Voyage;
use app\models\Reservation;
use app\models\RechercheForm;

class SiteController extends Controller {
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     * 
     * @return string
     */
    public function actionIndex() {

        $recherche = new RechercheForm();

        // Affichage de la page "pour la première fois" quand l'utilisateur a cliqué sur l'onglet
        return $this->render('index', [
            'recherche' => $recherche,
            'resultats' => null,
        ]);

    }

    /**
     * Search for available trips corresponding to the research made by the user.
     * 
     * @return string
     */
    public function actionRecherche() {

        $recherche = new RechercheForm();
        $resultats = null;

        // Initialise les attributs de l'instance RechercheForm à partir des valeurs transmises via le formulaire
        $recherche->load(Yii::$app->request->get(), 'RechercheForm');

        // Si la recherche est valide = pas d'erreurs détectée (voir la méthode rules())
        if($recherche->validate()) {
            $resultats = RechercheForm::lancerRecherche(
                $recherche->nb_personnes,
                false,  // pour le moment on ne traite pas encore les correspondances
                $recherche->ville_depart,
                $recherche->ville_arrivee
            );

            // Gestion de la notification du bandeau
            if($resultats) {
                // Messages différents dans la barre de notification selon si un ou plusieurs voyages ont été trouvés
                if(count($resultats) > 1) $notification = "Plusieurs voyages ont été trouvés correspondants à votre recherche !";
                else $notification = "Un voyage a été trouvé correspondant à votre recherche !";
            }
            else $notification = "Aucun voyage correspondant à votre recherche !";

            // Retourne les données via JSON
            return $this->asJson([
                'notification' => $notification,
                // renderAjax() ne retourne que la vue avec les modifications effectuées
                'html' => $this->renderAjax('_resultats', [     // les vues partielles sont nommées _resultats.php !
                    'resultats' => $resultats,
                    'recherche' => $recherche
                ])
            ]);

        }

        return $this->asJson([
            'notification' => "Recherche invalide !",
            'html' => "",
            'errors' => $recherche->getErrors(),
        ]);

    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays my user page for trying to retrieve data from a user.
     * 
     * @return string
     */
    public function actionTestUser() {

        // Mise en place de l'argument pseudo dans l'URL
        $request = Yii::$app->request;
        $pseudo = $request->get('pseudo');

        // Récupération de l'utilisateur dans la table à partir de la valeur de l'argument
        $user = User::findByPseudo($pseudo);

        if($user) {

            // Récupération d'une liste de voyages proposés par le conducteur si il a enregistré son permis
            if($user->permis != NULL) $voyages = Voyage::findVoyagesByUserId($user->id); 
            else $voyages = NULL;

            // Récupération d'une liste de réservations enregistrées par l'utilisateur 
            $reservations = Reservation::findReservationsByUserId($user->id);

            // Fournit à la view les instances des classes crées pour afficher leurs données
            return $this->render('test-user', ['user' => $user, 'voyages' => $voyages, 'reservations' => $reservations]);
        }
        else return $this->render('test-user', ['user' => $user]);

    }

}
