<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Garantie;
use App\Entity\InfoGarantie;
use App\Entity\InitPwd;
use App\Entity\Pannier;
use App\Entity\Prelevement;
use App\Entity\UserCreation;
use App\Form\InfoGarantieType;
use App\Form\InitPwdType;
use App\Form\PannierType;
use App\Form\PrelevementType;
use App\Form\UserType;
use App\Form\VehiculeType;
use App\Security\CustomVerySecureHasher;
use App\Security\LoginFormAuthenticator;
use App\Security\UserProvider;
use App\Service\PdfService;
use App\Service\SignRequestService;
use App\Shared\Shared;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use App\Entity\Vehicule;
use App\Entity\Contrat;
use App\Service\WebService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SouscriptionController extends AbstractController
{

    //Variable Globale
    private $wsdv;
    private $requestStack;
    private $session;
    private $contrat;
    private $param;


    public function __construct(WebService $wsdv, RequestStack $requestStack, ParameterBagInterface $params)
    {
        //Constructeur, on affecte les variable global
        $this->wsdv = $wsdv;
        $this->param = $params;
        $this->requestStack = $requestStack;
        $this->session = $this->requestStack->getSession();
        $this->contrat = new Contrat();
    }

    //Permet de savoir si un utilisateur connecté est inactif
    public function utilisateurInactif()
    {
        if ($this->getUser() != null) {
            if (time() - $this->session->getMetadataBag()->getLastUsed() > ($this->getParameter('TIMER_minute') * 60)) {
                $this->session->set('user_status', 'Involontaire');
                $this->session->set('contrat',null);
                $url = $this->generateUrl('app_logout');
                $response = new RedirectResponse($url);
                $response->send();
                exit;
            }
        }else{
            if ($this->session->isStarted() && time() - $this->session->getMetadataBag()->getLastUsed() > ($this->getParameter('TIMER_minute') * 60)) {
                $this->session->set('contrat',null);
                $url = $this->generateUrl('app_index');
                $response = new RedirectResponse($url);
                $response->send();
                exit;
            }
        }
    }

    //Permet de savoir si l'utilisateur est connecté
    public function utilisateurConnecte()
    {
        if ($this->getUser() == null) {
            $url = $this->generateUrl('app_index');
            $response = new RedirectResponse($url);
            $response->send();
            exit;
        }
    }


    //Fonction de création du contrat
    public function generateContrat($typeContrat, $contrat)
    {
        //On gère la dates
        $datesDossier = array(
            "dateCirculation" => date_format($this->contrat->getVehicule()->getDate(), "Y-m-d"),
            "dateGarantie" => date_format(new \DateTime, "Y-m-d"),
            "dateSouscription" => date_format(new \DateTime, "Ymd")
        );

        //Construction du json
        $jsonContrat = $this->wsdv->genrateContract($contrat, $datesDossier, true, "En Attente", $typeContrat);

        //Appel au Webservice pour ajouter le contrat dans la base
        $response = $this->wsdv->addNewContract($jsonContrat);
        if ($response->getStatusCode() == 200) {
            $contratData = json_decode($response->getContent(), true);

            //On récupère le Numéro de contrat et on l'affecte dans la variable session
            $contrat->setCode($contratData["code"]);
            $contrat->setLibelle($contrat->getGarantie()->getLibelle());
            $contrat->setNumeroContrat($contratData["contrat"]);
            $this->session->set("contrat", $contrat);
        }
    }

    //Page d'accueil
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        //On vérifie si l'utilisateur s'est déconnecter
        $user_status = $this->session->get('user_status');

        //Si il est déconnecter on vide la session
        if ($user_status == "Volontaire" | $user_status == "Involontaire") {
            $this->session->invalidate();
        }

        //Création du formulaire "Recherche par immat + Km"
        $form_immat = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_infos_vehicule'))
            ->add('immat', TextType::class, array('label' => false))
            ->add('km', NumberType::class, array('label' => false))
            ->add('calculer', SubmitType::class)
            ->getForm();

        //On récupère la liste des sinistres
      //  $sinistres = $this->wsdv->getSinitres();

        return $this->render('souscription/index.html.twig', [
            'form_immat' => $form_immat->createView(),
            'user_status' => $user_status,
            //'sinistres' => $sinistres,
            'TIMER_minute' => $this->getParameter('TIMER_minute'),
        ]);
    }

    //Page de saisie des informations du véhicule
    #[Route('/infos-vehicule', name: 'app_infos_vehicule', methods: ['POST', 'GET'])]
    public function infos_vehicule(Request $request, FormFactoryInterface $formFactory): Response
    {
        //Si l'utilisateur est inactif, on supprime les données de la session
        if ($this->session->isStarted() && time() - $this->session->getMetadataBag()->getLastUsed() > ($this->getParameter('TIMER_minute') * 60)) {
            $this->session->set('contrat',null);
        }

        //On défini l'étape de la souscription (Fil d'ariane)
        $etapeSouscripton = 1;

        //Création du formulaire de recherche par immatriculation
        $form_immat = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_infos_vehicule'))
            ->add('immat', TextType::class, array('label' => false))
            ->add('km', NumberType::class, array('label' => false))
            ->add('calculer', SubmitType::class)
            ->getForm();

        //On récupère la liste des marques par le webservice
        $liste_marque = $this->wsdv->getMarques();

        //On créer l'objet Véhicule
        $vehicule = new Vehicule();

        //Création d'un formulaire Véhicule (voir From/VehiculeType)
        $form_vehicule = $formFactory->createNamedBuilder('form', VehiculeType::class, $vehicule)->getForm();

        //On vérifie si des données sont dans la variable de session, si c'est le cas on pré-remplie le formulaire
        if ($this->session->get("contrat") != null) {

            $this->contrat = $this->session->get("contrat");

            //On récupére les données du véhicules qui sont déjà dans la session
            $sessionCar = $this->contrat->getVehicule();

            //On récupére, via un WS, la liste des modèles de voiture en fonction de la marque
            $resultat_lesmodeles = $this->wsdv->getModeles($sessionCar->getMarque());

            //On rempli le formulaire $form_data_car avec les information récupérer par les WS
            $form_vehicule = $formFactory->createNamedBuilder('form', VehiculeType::class, $sessionCar)->getForm();
            $form_vehicule->add('modele', ChoiceType::class, [
                'placeholder' => 'Sélectionnez le modèle*',
                'choices' => array_combine($resultat_lesmodeles, $resultat_lesmodeles),
                'data' => $sessionCar->getModele()
            ]);
            $form_vehicule->add('marque', ChoiceType::class, [
                'placeholder' => 'Sélectionnez la marque*',
                'choices' => array_combine($liste_marque, $liste_marque),
            ]);
        } else {

            //Pas de véhicule dans la session, on remplie juste les marques
            $form_vehicule->add('marque', ChoiceType::class, [
                'placeholder' => 'Sélectionnez la marque*',
                'choices' => array_combine($liste_marque, $liste_marque),
            ]);
        }

        //Lorsque le formulaire recherche par immatriculation est validé
        $form_immat->handleRequest($request);
        if ($form_immat->isSubmitted() && $form_immat->isValid()) {
            $date=new \DateTime('now');
            $this->session->set('time_session_request',strtotime($date->format('Y-m-d H:i:s')));

            //On recherche par immatriculation par Webservice et on mets la valeur du webservice dans l'objet + Km
            $vehicule = $this->wsdv->findByImmatriculation($form_immat->get("immat")->getData());
            $vehicule->setKilometrage($form_immat->get("km")->getData());

            //On enregistre le modele dans la session
            $this->session->set('modele', $vehicule->getModele());

            //On vérifie que le Webserice nous ai renvoyé des données et on les affecte au formulaire Véhicule
            if ($vehicule->getMarque() !== null) {

                //On recherche la liste des modèles avec la marque pour remplir le champs dans le formualire
                $resultat_lesmodeles = $this->wsdv->getModeles($vehicule->getMarque());
                $form_vehicule = $formFactory->createNamedBuilder('form', VehiculeType::class, $vehicule)->getForm();
                $form_vehicule->add('marque', ChoiceType::class, [
                    'placeholder' => 'Sélectionnez la marque*',
                    'choices' => array_combine($liste_marque, $liste_marque),
                ]);
                $form_vehicule->add('modele', ChoiceType::class, [
                    'placeholder' => 'Sélectionnez le modèle*',
                    'choices' => array_combine($resultat_lesmodeles, $resultat_lesmodeles),
                    'data' => $vehicule->getModele()
                ]);

                //On affiche la page avec la page avec le formulaire Véhicule pré-remplie
                return $this->render('souscription/infos_vehicule.html.twig', [
                    'etape_souscription' => $etapeSouscripton,
                    'form_immat' => $form_immat->createView(),
                    'form_vehicule' => $form_vehicule->createView(),
                    'bError' => 0,
                ]);
            } else {

                //On affiche la page avec une erreur
                return $this->render('souscription/infos_vehicule.html.twig', [
                    'etape_souscription' => $etapeSouscripton,
                    'form_immat' => $form_immat->createView(),
                    'form_vehicule' => $form_vehicule->createView(),
                    'bError' => 1,
                ]);
            }
        }

        //Quand le formulaire Véhicule est validé
        $form_vehicule->handleRequest($request);
        if ($form_vehicule->isSubmitted()) {

            //On récupère les données et on les stocks dans la variable session
            $recupererVehicule = $form_vehicule->getData();
            $recupererVehicule->setModele($this->session->get('modele'));
            $this->contrat->setVehicule(Vehicule::getVehicule($this->wsdv->objectToArray($recupererVehicule)));

            //On enregistre dans la variable session
            $this->session->set("contrat", $this->contrat);

            //On passe à la page suivante
            return $this->redirectToRoute('app_offres_garanties');
        }

        //Affichage de la page classique (formulaire vide)
        return $this->render('souscription/infos_vehicule.html.twig', [
            'etape_souscription' => $etapeSouscripton,
            'form_immat' => $form_immat->createView(),
            'form_vehicule' => $form_vehicule->createView(),
            'bError' => 0,
        ]);
    }

    //Page de choix de la garantie
    #[Route('/offres-garanties', name: 'app_offres_garanties', methods: ['GET', 'POST'])]
    public function offres_garanties(Request $request, Encryptor $encryptor, WebService $webService): Response
    {
        //Récupération des données contrat de la session
        $this->contrat = $this->session->get("contrat");

        //Si les données du vehicule ne sont pas renseignés, on renvoi sur la page d'accueil
        if ($this->contrat == null ||  $this->contrat->getVehicule()->vehiculeValide() === false) {
            return $this->redirectToRoute('app_index');
        }

        //Vérification de l'inactivité de l'utilisateur
        $this->utilisateurInactif();

        //On défini l'étape de la souscription (Fil d'ariane)
        $etapeSouscription = 2;

        //On vérifie si la méthode utiliser est la méthode POST (Choix de la garantie)
        if ($request->isMethod('post')) {

            //Encryptor permet de crypter les valeur du formulaire pour pas modifier le prix dans la page HTML
            $this->encryptor = $encryptor;
            $submittedToken = $request->request->get('token');

            //Vérification de la conformité du Token et si le JSON ne retourne aucune erreur
            if ($this->isCsrfTokenValid('data_Offre', $submittedToken) && json_last_error() === JSON_ERROR_NONE) {

                //On décode le JSON et on déchiffre les données afin de pouvoir les stocker dans un variable
                $dataGarantie = json_decode($this->encryptor->decrypt($request->request->get('data')), true);

                //On vide la variable de session et on enregistre la garantie choisie dans la variable de session
                $this->contrat->setGarantie(Garantie::setGarantie($dataGarantie));
                $this->session->set("contrat", $this->contrat);

                //On passe à la page de connexion
                return $this->redirectToRoute('app_info_client');
            }
        } else {

            //On met en forme la date
            $date = date_format($this->contrat->getVehicule()->getdate(), "Ymd");

            //Recherche des garanties disponibles
            $response = $webService->getTarification($this->contrat->getVehicule(), $date);

            //Si on à des garantie disponible, on affiche la liste
            if (count($response) != 0) {
                return $this->render('souscription/offres_garanties.html.twig', [
                    'etape_souscription' => $etapeSouscription,
                    'offres' => $response,
                    'premium' => Shared::DETAILS_PREMIUM,
                    'sweet' => Shared::DETAILS_SWEET,
                    'zen' => Shared::DETAILS_ZEN
                ]);
            } else {
                return $this->render('souscription/offres_garanties.html.twig', [
                    'etape_souscription' => $etapeSouscription,
                    'offres' => [],
                    'premium' => Shared::DETAILS_PREMIUM,
                    'sweet' => Shared::DETAILS_SWEET,
                    'zen' => Shared::DETAILS_ZEN
                ]);
            }
        }

        return $this->render('souscription/offres_garanties.html.twig', [
            'etape_souscription' => $etapeSouscription,
            'offres' => [],
            'premium' => Shared::DETAILS_PREMIUM,
            'sweet' => Shared::DETAILS_SWEET,
            'zen' => Shared::DETAILS_ZEN
        ]);
    }


    #[Route('/check-login', name: 'app_check_client', methods: ['POST'])]
    public function check_login(Request $request, UserProvider $userProvider, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator)
    {

        $this->contrat = $this->session->get("contrat");

        $this->contrat = $this->session->get("contrat");
        if ($this->contrat == null || $this->contrat->getGarantie()->garantieValide() === false || $this->contrat->getVehicule()->vehiculeValide() === false) {
            return $this->redirectToRoute('app_index');
        }

        //Vérification de l'inactivité de l'utilisateur
        $this->utilisateurInactif();

        $password = $request->request->get('password');
        $username = $request->request->get('email');
        $user = $userProvider->loadUserByIdentifier($username);
        if ($user) {
            $csv = iconv("UTF-8", "Windows-1252", $this->param->get('app.hash_key'));
            $passHash = strtoupper(hash_hmac('sha256', $password, $csv));

            if ($user->getPassword() == null) {
                $this->session->set('errorLogin', true);
                return $this->redirectToRoute('app_info_client');
            }

            if (hash_equals($passHash, $user->getPassword())) {

                $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
                $this->contrat->setClient(Client::loginClient($this->wsdv->objectToArray($user)));
                $this->session->set("contrat", $this->contrat);
                return $this->redirectToRoute('app_modif_client');
            } else {
                $this->session->set('errorLogin', true);
                return $this->redirectToRoute('app_info_client');
            }
        } else {
            return $this->redirectToRoute('app_info_client');
        }
    }

    #[Route('/info-client', name: 'app_info_client', methods: ['GET', 'POST'])]
    public function info_client(Request $request, FormFactoryInterface $formFactory, UserPasswordHasherInterface $passwordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, MailerInterface $mailer): Response
    {

        $this->contrat = $this->session->get("contrat");

        //Si les données de garantie ne sont pas renseignées, on renvoi sur la page d'accueil
        $this->contrat = $this->session->get("contrat");
        if ($this->contrat == null || $this->contrat->getGarantie()->garantieValide() === false || $this->contrat->getVehicule()->vehiculeValide() === false) {
            return $this->redirectToRoute('app_index');
        }

        //Vérification de l'inactivité de l'utilisateur
        $this->utilisateurInactif();

        /* Message d'erreur */
        $errorRegister = 0;
        $errorCompte = 0;
        $errorMessage = "";
        $errorLogin = false;
        // recuperation la contrat

        /* Etape Souscription */
        $etapeSouscription = 3;
        /* Initialisation formulaire creation d'utilisateur */
        $userCreation = new UserCreation();
        $form_create = $formFactory->createNamedBuilder('form', UserType::class, $userCreation)->getForm();
        $form_create
            ->add('mail', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'Les emails des passes faut etre identiques',
                'required' => true,
                'first_options'  => ['label' => 'Mot de Passe'],
                'second_options' => ['label' => 'Confirmer mot de Passe'],
            ])
            ->add('pwd', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'required' => true,
                'first_options'  => ['label' => 'Mot de Passe'],
                'second_options' => ['label' => 'Confirmer mot de Passe'],
            ])
            ->add('accepte_mail', CheckboxType::class, [
                'required' => false,
            ])
            ->add('CGU', CheckboxType::class, [
                'required' => true,
            ])
            ->add('creation', SubmitType::class);
        $form_create->handleRequest($request);
        if ($form_create->isSubmitted() && $form_create->isValid()) {
            $data = $this->wsdv->objectToArray($form_create->getData());
            $data['accepte_mail'] = $form_create->get('accepte_mail')->getData();
            $message = $this->render('emails/confirm_user.html.twig', [
                'user' => $data,
            ]);

            //On envoie un mail et on affiche la page de confirmation
            $messageContent = 'La Belle Garantie : Confirmation de la création de votre compte';
            $checkMail = $this->wsdv->checkMailExist($this->param->get('app.email'), $form_create->get('mail')->getData(), $mailer, $messageContent, $message);
            if ($checkMail) {
                $user = $this->wsdv->ajouterClient($data);
                if ($user != null) {
                    $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );
                    $this->contrat->setClient(Client::initClient($this->wsdv->objectToArray($user)));
                    $this->session->set("contrat", $this->contrat);
                    return $this->redirectToRoute('app_modif_client');
                } else {
                    //Erreur lors de la création du compte
                    $this->session->set("errorRegister", true);
                    $this->session->set("errorRegisterMessage", 'Un compte est déjà existant pour cette adresse mail.');
                    return $this->redirectToRoute('app_info_client');
                }
            } else {
                $this->session->set("errorRegister", true);
                $this->session->set("errorRegisterMessage", "Email n'existe pas");
                return $this->redirectToRoute('app_info_client');
            }
        }

        //On vérifie qu'il n'y a pas eu d'erreur durant la création du compte "Security controlleur"
        if ($this->session->get('errorRegister') === true) {
            $errorRegister = 1;
            $errorMessage = $this->session->get('errorRegisterMessage');
            $this->session->remove('errorRegister');
            $this->session->remove('errorRegisterMessage');
        }

        //On vérifier qu'il n'y a pas eu d'erreur durant la connexion "LoginFormAuthentificator"
        if ($this->session->get('errorLogin') === true) {
            $errorLogin = true;
            $this->session->remove('errorLogin');
        }

        return $this->render('souscription/info_client.html.twig', [
            'form_create' => $form_create->createView(),
            'etape_souscription' => $etapeSouscription,
            'error' => $errorLogin,
            'errorRegister' => $errorRegister,
            'errorMessage' => $errorMessage,
        ]);
    }
    #[Route('/modif-client', name: 'app_modif_client', methods: ['POST', 'GET'])]
    public function modif_client(Request $request, FormFactoryInterface $formFactory): Response
    {
        $this->contrat = $this->session->get("contrat");

        //Si les données de garantie ne sont pas renseignées, on renvoi sur la page d'accueil
        if ($this->contrat == null || $this->contrat->getGarantie()->garantieValide() === false) {
            return $this->redirectToRoute('app_index');
        }

        //Vérification utilisateur connecté
        $this->utilisateurConnecte();

        //Vérification de l'inactivité de l'utilisateur
        $this->utilisateurInactif();

        $etapeSouscription = 3;
        $this->contrat = $this->session->get("contrat");
        $user = $this->getUser();
        $userModify = UserCreation::initUser($user);

        //Création du formulaire de modification données personnelles de compte
        $form_modif = $formFactory->createNamedBuilder('form', UserType::class, $userModify)->getForm();
        $form_modif->add('Valider', SubmitType::class);

        $form_modif->handleRequest($request);
        if ($form_modif->isSubmitted() && $form_modif->isValid()) {

            //Création du JSON pour la mise à jour des données
            $jsonUpdate = $this->wsdv->objectToArray($form_modif->getData());
            $jsonUpdate = json_encode($jsonUpdate);

            //envoie requette de  modifier client
            $response = $this->wsdv->updateClient($this->getUser()->getEmail(), $jsonUpdate);

            if ($response->getStatusCode() == 200) {
                //Compte modifié, on met à jour l'objet USER, CLIENT et la variable de session "contrat"
                $this->getUser()->setCivilite($form_modif->get('civilite')->getData());
                $this->getUser()->setNom($form_modif->get('nom')->getData());
                $this->getUser()->setPrenom($form_modif->get('prenom')->getData());
                $this->getUser()->setAdresse($form_modif->get('adresse')->getData());
                $this->getUser()->setCodePostal($form_modif->get('codePostal')->getData());
                $this->getUser()->setVille($form_modif->get('ville')->getData());
                $this->getUser()->setTelephone($form_modif->get('telephone')->getData());
                $this->getUser()->setAccepteMail($form_modif->get('accepte_mail')->getData());
                $this->contrat->setClient(Client::initClient($this->wsdv->objectToArray($this->getUser())));
                $this->session->set("contrat", $this->contrat);

                return $this->redirectToRoute('app_info_garantie');
            } else {
                return  $this->render('souscription/modif_client.html.twig', [
                    'form_modif' => $form_modif->createView(),
                    'etape_souscription' => $etapeSouscription,
                    'error' => 1
                ]);
            }
        }

        return  $this->render('souscription/modif_client.html.twig', [
            'form_modif' => $form_modif->createView(),
            'etape_souscription' => $etapeSouscription,
            'error' => 0
        ]);
    }

    #[Route('/info-garantie', name: 'app_info_garantie', methods: ['POST', 'GET'])]
    public function infoGarantie(Request $request, FormFactoryInterface $formFactory): Response
    {

        $this->contrat = $this->session->get("contrat");
        //Si les données de garantie ne sont pas renseignées, on renvoi sur la page d'accueil
        if ($this->contrat == null || $this->contrat->getGarantie()->garantieValide() === false || $this->contrat->getVehicule()->vehiculeValide() === false || $this->contrat->getClient()->clientValide() === false) {
            return $this->redirectToRoute('app_index');
        }

        //Vérification utilisateur connecté
        $this->utilisateurConnecte();

        //Vérification de l'inactivité de l'utilisateur
        $this->utilisateurInactif();

        // etape de souscription
        $etapeSouscription = 4;
        // recupere contrat

        $dataDate = '';
        // initialisation de la date de garantie de la session
        if (!$this->contrat->getGarantie()->getDateDebut()) {
            $this->contrat->getGarantie()->setDateDebut(date_format(new \DateTime('now'), "d/m/Y"));
            $this->session->set("contrat", $this->contrat);
            $dataDate = explode("/", $this->contrat->getGarantie()->getDateDebut());
        } else {
            if (gettype($this->contrat->getGarantie()->getDateDebut()) == "object") {
                $dataDate = explode("/", date_format($this->contrat->getGarantie()->getDateDebut(), "d/m/Y"));
            } else {
                $dataDate = explode("/", date_format(\DateTime::createFromFormat('d/m/Y', $this->contrat->getGarantie()->getDateDebut()), "d/m/Y"));
            }
        }
        // Initialisation formulaire
        $infoGarantie = new InfoGarantie();
        $infoGarantie->setDay($dataDate[0]);
        $infoGarantie->setMonth($dataDate[1]);
        $infoGarantie->setYear($dataDate[2]);
        $formGarantie = $formFactory->createNamedBuilder(
            'form',
            InfoGarantieType::class,
            $infoGarantie,
        )->getForm();
        $formGarantie->handleRequest($request);
        if ($formGarantie->isSubmitted() && $formGarantie->isValid()) {
            $dateDebutContrat = $formGarantie->get('day')->getData() . '/' . $formGarantie->get('month')->getData() . '/' . $formGarantie->get('year')->getData();

            $this->contrat->getGarantie()->setDateDebut(\DateTime::createFromFormat('d/m/Y', $dateDebutContrat));
            $date = date_format(new \DateTime('now'), "Y-m-d");
            $checkDate = date_format($this->contrat->getGarantie()->getDateDebut(), "Y-m-d");
            
            if (strtotime($date) <= strtotime($checkDate)) {
                $this->session->set("contrat", $this->contrat);
                return $this->redirectToRoute('app_pannier');
            } else {
                return $this->render('souscription/info_garantie.html.twig', [
                    'etape_souscription' => $etapeSouscription,
                    'contrat' => $this->contrat,
                    'premium' => Shared::DETAILS_PREMIUM,
                    'sweet' => Shared::DETAILS_SWEET,
                    'zen' => Shared::DETAILS_ZEN,
                    'form' => $formGarantie->createView(),
                ]);
            }
        }
        return $this->render('souscription/info_garantie.html.twig', [
            'etape_souscription' => $etapeSouscription,
            'contrat' => $this->contrat,
            'premium' => Shared::DETAILS_PREMIUM,
            'sweet' => Shared::DETAILS_SWEET,
            'zen' => Shared::DETAILS_ZEN,
            'form' => $formGarantie->createView(),
        ]);
    }
    #[Route('/fiche-conseil', name: 'app_fiche_client_download')]
    public function downloadFicheConseil(PdfService $pdfService)
    {
        $this->contrat = $this->session->get("contrat");
        $pdf = $pdfService->pdfFicheConseil($this->contrat);

        return new Response($pdf, 200, array(
            'Content-Type' => 'application/pdf',
        ));
        // return $pdf;
    }
    #[Route('/resetpassword/{param}', name: 'app_init_pwd', methods: ['POST', 'GET'])]
    public function initPwd(Request $request, FormFactoryInterface $formFactory, UserProvider $userProvider, $param)
    {

        // etape souscription
        $etapeSouscription = 3;
        // init form
        $initPwd = new InitPwd();
        $formInitPwd = $formFactory->createNamedBuilder('form', InitPwdType::class, $initPwd,)->getForm();
        $formInitPwd->handleRequest($request);
        if ($formInitPwd->isSubmitted() && $formInitPwd->isValid()) {
            $body = array('password' => $formInitPwd->get('pwd')->getData(), 'key' => $param);
            $jsonUpdate = json_encode($body);
            $respnse = $this->wsdv->resetPassword($jsonUpdate);
            if ($respnse->getStatusCode() == 200) {
                return $this->redirectToRoute('app_info_client');
            } else {
                return $this->render('souscription/forgot_password.html.twig', [
                    'etape_souscription' => $etapeSouscription,
                    'form' => $formInitPwd->createView(),
                    'error' => 1
                ]);
            }
        }
        return $this->render('souscription/forgot_password.html.twig', [
            'etape_souscription' => $etapeSouscription,
            'form' => $formInitPwd->createView(),
            'error' => 0
        ]);
    }
    #[Route('/panier', name: 'app_pannier', methods: ['POST', 'GET'])]
    public function pannier(Request $request, FormFactoryInterface $formFactory): Response
    {
        $this->contrat = $this->session->get("contrat");
        //Si les données de garantie ne sont pas renseignées, on renvoi sur la page d'accueil
        if ($this->contrat == null || $this->contrat->getGarantie()->garantieValide() === false || $this->contrat->getVehicule()->vehiculeValide() === false || $this->contrat->getClient()->clientValide() === false) {
            return $this->redirectToRoute('app_index');
        }


        //Vérification utilisateur connecté
        $this->utilisateurConnecte();

        //Vérification de l'inactivité de l'utilisateur
        $this->utilisateurInactif();

        // etape de souscription
        $etapeSouscription = 5;

        // formulaire promo
        $formPromo = $this->createFormBuilder()
            ->add('code', TextType::class, [
                'required' => true,
            ])
            ->add('OK', SubmitType::class, ['label' => 'OK'])
            ->getForm();
        // execution formulaire Promo

        // fomulaire pannier
        $pannier = new Pannier();
        $formPannier = $formFactory->createNamedBuilder(
            'form',
            PannierType::class,
            $pannier
        )->getForm();
        // execution formulaire
        $formPannier->handleRequest($request);
        if ($formPannier->isSubmitted() && $formPannier->isValid()) {
            $this->contrat->setTypePaiement($this->wsdv->choicePaiementMode($formPannier->get('prelevement')->getData()));
            $this->session->set("contrat", $this->contrat);
            $typeContrat = '';
            if ($formPannier->get('prelevement')->getData() == '1foiscb') {
                $typeContrat = 'Paiement par carte bancaire';
                $this->generateContrat($typeContrat, $this->contrat);
                return $this->redirectToRoute('app_paiement_cb');
            }
            $typeContrat = 'Paiement par prélèvement';
            $this->generateContrat($typeContrat, $this->contrat);
            return $this->redirectToRoute('app_paiement');
        }
        $formPromo->handleRequest($request);
        if ($formPromo->isSubmitted() && $formPromo->isValid()) {
            return $this->redirectToRoute('app_paiement');
        }


        return $this->render('souscription/pannier.html.twig', [
            'etape_souscription' => $etapeSouscription,
            'promo' => $formPromo->createView(),
            'pannier' => $formPannier->createView(),
            'contrat' => $this->contrat,
            'premium' => Shared::DETAILS_PREMIUM,
            'sweet' => Shared::DETAILS_SWEET,
            'zen' => Shared::DETAILS_ZEN,
        ]);
    }

    #[Route('/paiement', name: 'app_paiement', methods: ['POST', 'GET'])]
    public function paiement(Request $request, FormFactoryInterface $formFactory): Response
    {

        //Vérification utilisateur connecté
        $this->utilisateurConnecte();

        //Vérification de l'inactivité de l'utilisateur
        $this->utilisateurInactif();

        // etape souscription
        $etapeSouscription = 6;
        // initialitation contrat
        $this->contrat = $this->session->get("contrat");

        //Si les données de garantie ne sont pas renseignées, on renvoi sur la page d'accueil
        if ($this->contrat == null || $this->contrat->getGarantie()->garantieValide() === false || $this->contrat->getVehicule()->vehiculeValide() === false || $this->contrat->getClient()->clientValide() === false) {
            return $this->redirectToRoute('app_index');
        }
        // initialisation de formulaire
        $prelevement = new Prelevement();
        $prelevement->setNomPrenom($this->contrat->getClient()->getNom().' '.$this->contrat->getClient()->getPrenom());
        $prelevement->setAdresse($this->contrat->getClient()->getAdresse());
        $prelevement->setVille($this->contrat->getClient()->getVille());
        $prelevement->setCp($this->contrat->getClient()->getCodePostal());
        $formPrelevement = $formFactory->createNamedBuilder('form', PrelevementType::class, $prelevement)->getForm();
        $formPrelevement->handleRequest($request);
        if ($formPrelevement->isSubmitted() && $formPrelevement->isValid()) {
            $this->contrat->setPrelevement(Prelevement::initPrelevement($this->wsdv->objectToArray($formPrelevement->getData())));
            $typeContrat = 'Paiement par prélèvement';
            $jsonUpdateContrat = $this->wsdv->generateUpdateContrat($this->contrat, $typeContrat);
            $response = $this->wsdv->updateNewContract($jsonUpdateContrat);
            if ($response->getStatusCode() == 200) {

                $this->session->set("contrat", $this->contrat);
                return $this->redirectToRoute('app_confirm');
            }
            return $this->redirectToRoute('app_confirm');
        }

        return $this->render('souscription/paiement.html.twig', [
            'etape_souscription' => $etapeSouscription,
            'form' => $formPrelevement->createView(),
            'contrat' => $this->contrat,
            'premium' => Shared::DETAILS_PREMIUM,
            'sweet' => Shared::DETAILS_SWEET,
            'zen' => Shared::DETAILS_ZEN,
        ]);
    }
    #[Route('/confirmation', name: 'app_confirm')]
    public function confirmation(SignRequestService $signRequest, MailerInterface $mailer)
    {
        //Vérification utilisateur connecté
        $this->utilisateurConnecte();

        // etape souscription
        $etapeSouscription = 6;

        // init data
        $this->contrat = $this->session->get("contrat");

        //Si les données de garantie ne sont pas renseignées, on renvoi sur la page d'accueil
        if ($this->contrat == null || $this->contrat->getGarantie()->garantieValide() === false || $this->contrat->getVehicule()->vehiculeValide() === false || $this->contrat->getClient()->clientValide() === false) {
            return $this->redirectToRoute('app_index');
        }

        //Ajout du contrat dans l'espace client
        $jsonEspc = $this->wsdv->generateContractForClientWorkspace($this->contrat, 'Prélèvement');
        //On envoie un mail et on affiche la page de confirmation
        $messageContent = 'La Belle Garantie : Confirmation de souscription de garantie';
        // Envoi Email de contrat
        $message = $this->render('emails/mail_confirmation.html.twig', [
            'contrat' => $this->contrat,
        ]);

        $this->wsdv->sendEmail($this->param->get('app.email'), $this->contrat->getClient()->getEmail(), $mailer, $messageContent, $message);
        //Appel au Webservice d'ajout dans l'espace client
        $response = $this->wsdv->sendDataToClientWorkspace($jsonEspc);
        try {
            $signRequest->generateSignature($this->contrat);
        } catch (\Exception $exception) {
            $this->session->set("contrat", null);

            return $this->render('souscription/confirmation.html.twig', [
                'etape_souscription' => $etapeSouscription,
                'contrat' => $this->contrat
            ]);
        }
        $this->session->set("contrat", null);

        return $this->render('souscription/confirmation.html.twig', [
            'etape_souscription' => $etapeSouscription,
            'contrat' => $this->contrat
        ]);
    }

    #[Route('/checkout', name: 'app_paiement_cb', methods: ['POST', 'GET'])]
    public function checkout(Request $request)
    {
        $this->contrat = $this->session->get("contrat");
        $this->contrat->setMoyenPaiement("Paiement par carte bancaire");
        $this->session->set("contrat", $this->contrat);

        $urlImageOffre = '';
        if ($this->contrat->getGarantie()->getLibelle() == 'PREMIUM')
            $urlImageOffre = 'https://www.labellegarantie.com/build/img/premium.png';
        else if ($this->contrat->getGarantie()->getLibelle() == 'ZEN')
            $urlImageOffre = 'https://www.labellegarantie.com/build/img/zen.png';
        else if ($this->contrat->getGarantie()->getLibelle() == 'SWEET')
            $urlImageOffre = 'https://www.labellegarantie.com/build/img/sweet.png';

        \Stripe\Stripe::setApiKey($this->param->get('app.stripe_token'));
        header('Content-Type: application/json');

        $YOUR_DOMAIN = $request->getSchemeAndHttpHost();

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $this->contrat->getGarantie()->getMtTTC() * 100,
                    'product_data' => [
                        'name' => $this->contrat->getGarantie()->getLibelle(),
                        'description' => 'Garantie panne mécanique ' . $this->contrat->getGarantie()->getLibelle(),
                        'images' => [$urlImageOffre],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/checkout/success',
            'cancel_url' => $YOUR_DOMAIN . '/checkout/cancel',
            'customer_email' => $this->getUser()->getUsername(),              
            
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        return $this->redirect($checkout_session->url);
    }

    #[Route('/checkout/success', name: 'app_paiement_cb_sucess', methods: ['POST', 'GET'])]
    public function checkout_success()
    {

        $this->contrat = $this->session->get("contrat");
        $typeContrat = 'Paiement par carte bancaire';
        $jsonUpdateContrat = $this->wsdv->generateUpdateContrat($this->session->get("contrat"), $typeContrat);
        $response = $this->wsdv->updateNewContract($jsonUpdateContrat);
        if ($response->getStatusCode() == 200) {

            $this->session->set("contrat", $this->contrat);
            return $this->redirectToRoute('app_confirm');
        } else {
            return $this->redirectToRoute('app_index');
        }
    }

    #[Route('/checkout/cancel', name: 'app_paiement_cb_cancel', methods: ['POST', 'GET'])]
    public function checkout_cancel()
    {

        return $this->render('souscription/cancel.html.twig', []);
    }
}
