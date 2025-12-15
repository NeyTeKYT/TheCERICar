<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\db\Query;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\Voyage;
use app\models\Reservation;
use app\models\RechercheForm;
use app\models\Trajet;
use app\models\RegistrationForm;

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

            // Vérification que le trajet entré par l'utilisateur existe dans la BDD
            $trajet_recherche = Trajet::getTrajet($recherche->ville_depart, $recherche->ville_arrivee);
            if(!$trajet_recherche) $notification = "Le trajet renseigné est indisponible !";

            else if($resultats) {
                // Messages différents dans la barre de notification selon si un ou plusieurs voyages ont été trouvés
                $nb_voyages_dispo = 0;
                foreach($resultats as $voyage) if(Voyage::verifierDisponibilite($voyage->id, $recherche->nb_personnes)) $nb_voyages_dispo++;
                if($nb_voyages_dispo > 1) $notification = "Plusieurs voyages ont été trouvés correspondants à votre recherche !";
                else if($nb_voyages_dispo == 1) $notification = "Un voyage a été trouvé correspondant à votre recherche !"; 
                else $notification = "Tous les voyages disponibles ne permettent pas d'accueillir $recherche->nb_personnes passagers !";
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
     * Allows the user to get an access to the view corresponding to the trip booking or else is redirected to the login form.
     */
    public function actionReserver($id_voyage, $nb_personnes) {

        // Si l'utilisateur n'est pas connecté, alors il est redirigé vers le formulaire de connexion puis sera redirigé vers la page pour réserver le voyage
        if(Yii::$app->user->isGuest) {
            Yii::$app->user->setReturnUrl(Yii::$app->request->url);
            return $this->redirect(['site/login']);
        }

        // Récupération de l'instance voyage
        $voyage = Voyage::findOne($id_voyage);

        // Si le voyage n'existe pas = gestion de l'id_voyage car on ne peut pas faire confiance au client !
        if(!$voyage) throw new \yii\web\NotFoundHttpException('Voyage innexistant.');

        // Vérifie la disponibilité du voyage en fonction du nombre de personnes
        if(!Voyage::verifierDisponibilite($voyage->id, $nb_personnes)) {
            Yii::$app->session->setFlash('error', 'Plus assez de places disponibles.');
            return $this->redirect(['site/index']);
        }

        return $this->render('reserver', [
            'voyage' => $voyage,
            'nb_personnes' => $nb_personnes,
        ]);
    }

    public function actionConfirmerReservation() {
        if(Yii::$app->user->isGuest) return $this->redirect(['site/login']);

        $voyageId = Yii::$app->request->post('voyage_id');
        $nb = Yii::$app->request->post('nb');

        $reservation = new Reservation();
        $reservation->user_id = Yii::$app->user->id;
        $reservation->voyage_id = $voyageId;
        $reservation->nb_personnes = $nb;
        $reservation->date_reservation = date('Y-m-d H:i:s');

        if($reservation->save()) {
            Yii::$app->session->setFlash('success', 'Réservation confirmée 🎉');
            return $this->redirect(['site/test-user', 'pseudo' => Yii::$app->user->identity->username]);
        }

        Yii::$app->session->setFlash('error', 'Erreur lors de la réservation.');
        return $this->redirect(['site/index']);
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin() {
        if(!Yii::$app->user->isGuest) return $this->goHome();

        $model = new LoginForm();

        if($model->load(Yii::$app->request->post())) {

            if($model->login()) {

                if(Yii::$app->request->isAjax) {
                    return $this->asJson([
                        'success' => true,
                        'notification' => "Connexion réussie ! Vous allez être automatiquement redirigé vers la page d'accueil.",
                    ]);
                }

                return $this->goBack();
            }

            // Erreur de login
            if (Yii::$app->request->isAjax) {
                return $this->asJson([
                    'success' => false,
                    'notification' => "Identifiants incorrects.",
                    'errors' => $model->getErrors(),
                ]);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Registration action
     * 
     * @return Response|string
     */
    public function actionInscription() {
        if(!Yii::$app->user->isGuest) return $this->goHome();

        $model = new RegistrationForm();

        if($model->load(Yii::$app->request->post())) {

            if($model->register()) {

                if(Yii::$app->request->isAjax) {
                    return $this->asJson([
                        'success' => true,
                        'notification' => "Compte créé avec succès 🎉",
                    ]);
                }

                return $this->goHome();
            }

            if(Yii::$app->request->isAjax) {
                return $this->asJson([
                    'success' => false,
                    'notification' => "Erreur lors de l’inscription.",
                    'errors' => $model->getErrors(),
                ]);
            }
        }

        return $this->render('inscription', ['model' => $model]);
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
