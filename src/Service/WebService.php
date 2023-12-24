<?php

namespace App\Service;

use App\Security\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Vehicule;

class WebService
{

    private $param;
    private $client;

    public function __construct(ParameterBagInterface $params, HttpClientInterface $client){
        $this->param = $params;
        $this->client = $client;
    }

    public function findByImmatriculation($immatriculation) : Vehicule{

        //Appel du webservice immatriculation
        $response = $this->client->request(
            'GET',
            $this->param->get('app.wsdv_url') . 'vehicule/immat/' . urlencode($immatriculation) . '',
            [
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')]
            ]
        );

        $vehicule = new Vehicule();

        //Récupération code HTTP
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            
            //Traitement de la réponse
            $data = $response->toArray();
            $vehicule->setMarque($data["reponse"]["marque"]);
            $vehicule->setModele($data["reponse"]["modeleEtude"]);
            $vehicule->setImmatriculation($data["reponse"]["immatSiv"]);
            $vehicule->setPrix($data["reponse"]["prixVehic"]);
            $vehicule->setEnergie($data["reponse"]["energie"]);
            $vehicule->setDate(new \DateTime($data["reponse"]["date1erCir"]));
            $vehicule->setSerie($data["reponse"]["nSerie"]);
            $vehicule->setMine($data["reponse"]["type"]);

        }

        return $vehicule;

    }

    public function getMarques(): Array{

        $response = $this->client->request(
            'GET',
            $this->param->get('app.wsdv_url') . 'vehicule/marques',
            [
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')]
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {

            $marques = $response->toArray();
            return $marques["reponse"];

        }else{

            return $marques = [];
            
        }

    }

    public function getModeles(string $marque): Array{

        $response = $this->client->request(
            'GET',
            $this->param->get('app.wsdv_url') . 'vehicule/modeles?marque=' . urlencode($marque),
            [
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')]
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {

            $modeles = $response->toArray();
            $liste_modele = array();

                foreach ($modeles["reponse"] as $new_modele) {
                    array_push($liste_modele, $new_modele["nom"]);
                }

            return $liste_modele;

        }else{

            return $liste_modele = [];
            
        }

    }
    //Mise à jour des données du client
    public function updateClient($email,$body){
        $response = $this->client->request(
            'PUT',
            $this->param->get('app.wsdv_url') . 'espace/compte/' . urlencode($email),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')],
                'body' => $body
            ]
        );
        return $response;
    }
    public function ajouterClient($body){

        $dataJson = array(
            "civilite" => $body["civilite"],
            "nom" => $body["nom"],
            "prenom" => $body["prenom"],
            "adresse" => $body["adresse"],
            "codePostal" => $body["codePostal"],
            "ville" => $body["ville"],
            "telephone" => $body["telephone"],
            "mail" => $body["mail"],
            "password" => $body['pwd'],
            "accepte_mail" => $body["accepte_mail"] ? ($body["accepte_mail"] == "1" ? "Oui" : "Non") : "Non",
            "source" => "LBG"
        );

        $dataJson = json_encode($dataJson);
        $response = $this->client->request(
            'POST',
            $this->param->get('app.wsdv_url') . 'espace/compte',

            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')],
                'body' => $dataJson
            ]
        );
        if($response->getStatusCode() == 200){
            $dataWS = $response->toArray();
            $user = new User();
            $user->setId($dataWS["compte"]["id"]);
            $user->setCivilite($dataWS["compte"]["civilite"]);
            $user->setNom($dataWS["compte"]["nom"]);
            $user->setPrenom($dataWS["compte"]["prenom"]);
            $user->setAdresse($dataWS["compte"]["adresse"]);
            $user->setVille($dataWS["compte"]["ville"]);
            $user->setCodePostal($dataWS["compte"]["codePostal"]);
            $user->setTelephone($dataWS["compte"]["telephone"]);
            $user->setEmail($dataWS["compte"]["mail"]);
            $user->setPassword($dataWS["compte"]["password"]);
            $user->setAccepteMail($dataWS["compte"]["accepte_mail"]);
            return $user;
        }
        else{
           return  null;

        }
    }
    public function getTarification($vehicule,$date){
        $response= $this->client->request(
            'GET',
            $this->param->get('app.wsdv_url') . 'tarification?marque=' . urlencode($vehicule->getMarque()) . '&modele=' . urlencode('S80') . '&km=' . urlencode($vehicule->getKilometrage()) . '&prix=' . urlencode($vehicule->getPrix())  . '&date=' . urlencode($date) . '',
            [
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')]
            ]
        );
        $statusCode=$response->getStatusCode();
        if($statusCode == 200){
            $tarification=$response->toArray();
            return $tarification['reponse'];
        }
        else{
            return [];
        }
    }
    public function getSinitres(){
        $response= $this->client->request(
            'GET',
            $this->param->get('app.wsdv_url') . 'sinistres/actualite' ,
            [
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')]
            ]
        );
        $statusCode=$response->getStatusCode();
        if($statusCode == 200){
            $tarification=$response->toArray();
            return $tarification['reponse'];
        }
        else{
            return [];
        }
    }
    //Convertion d'un objet en tableau
    function objectToArray($object) : array
    {
        $output = [];
        foreach ((array)$object as $key => $value) {
            if($value != null)
                $output[preg_replace('/\000(.*)\000/', '', $key)] = $value;
        }

        return $output;
    }
    // renit mot de passe
    public function reinitPassword($body){
        $response = $this->client->request(
            'POST',
            $this->param->get('app.wsdv_url') . 'espace/lbg/password/lien',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')],
                'body' => $body
            ]
        );
        return $response;
    }
    //Création du contrat
    public function addNewContract($body){
        $response = $this->client->request(
            'POST',
            $this->param->get('app.wsdv_url') . 'contrat/labellegarantie',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')],
                'body' => $body
            ]
        );
        return $response;
    }
    //mise à jour du contrat
    public function updateNewContract($body){
        $response = $this->client->request(
            'PUT',
            $this->param->get('app.wsdv_url') . 'contrat/paiement',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')],
                'body' => $body
            ]
        );
        return $response;
    }
    // generate update contrat

    public function generateUpdateContrat($contrat,$typepaiement){
       $jsonUpdatedContrat=array(
           "contrat" => $contrat->getNumeroContrat(),
           "moyen_paiement" => $typepaiement,
           "type_reglement" => $contrat->getTypePaiement(),
           "reg_iban" => $contrat->getPrelevement()->getIban()? $contrat->getPrelevement()->getIban() :'' ,
           "reg_bic" => $contrat->getPrelevement()->getBic() ? $contrat->getPrelevement()->getBic() : '',
           "reg_nom" => $contrat->getPrelevement()->getNomPrenom() ? $contrat->getPrelevement()->getNomPrenom() :'',
           "reg_adresse" => $contrat->getPrelevement()->getAdresse() ? $contrat->getPrelevement()->getAdresse() :'',
           "reg_cp" => $contrat->getPrelevement()->getCp() ? $contrat->getPrelevement()->getCp() :'',
           "reg_ville" => $contrat->getPrelevement()->getVille() ? $contrat->getPrelevement()->getVille() :'',
           "etat" => "Payé"
       );
        return json_encode($jsonUpdatedContrat);
    }

    //Génération du JSON du contrat
    public function genrateContract($contrat,$datesDossier,bool $prelevement,$etat,$typeContrat){

        $jsonContrat = array(
            "idCommande" => "",
            "date_souscription" => $datesDossier['dateSouscription'],
            "type_reglement" => $typeContrat,
            "moyen_paiement" => $contrat->getTypePaiement(),
            "code_promo" => "",
            "id_promo" => "",
            "prix" => $contrat->getGarantie()->getMtTTC(),
            "reduction" => "",
            "total_ttc" => $contrat->getGarantie()->getMtTTC(),
            "etat" => $etat,
            "id_client" => 0,
            "date_inscription" => "",
            "civilite" => $contrat->getClient()->getCivilite(),
            "nom" => $contrat->getClient()->getNom(),
            "prenom" => $contrat->getClient()->getPrenom(),
            "adresse" => $contrat->getClient()->getAdresse(),
            "code_postal" => $contrat->getClient()->getCodePostal(),
            "ville" => $contrat->getClient()->getVille(),
            "telephone" => $contrat->getClient()->getTelephone(),
            "mail" => $contrat->getClient()->getEmail(),
            "mot_de_passe" => "",
            "situation_client" => "",
            "situation_client_2" => "",
            "cgv" => "",
            "acceptation_mail" => $contrat->getClient()->getAccepteMail(),
            "langue" => "",
            "etat_client" => "",
            "id_vehicule" => "",
            "id_marque" => 0,
            "marque" => $contrat->getVehicule()->getMarque(),
            "id_modele" => 0,
            "modele" => $contrat->getVehicule()->getModele(),
            "prix_veh" => $contrat->getVehicule()->getPrix(),
            "dt_circulation" => $datesDossier['dateCirculation'],
            "kilometrage" => $contrat->getVehicule()->getKilometrage(),
            "puissance_fisc" => 0,
            "offre" => $contrat->getGarantie()->getCode(),
            "duree" => $contrat->getGarantie()->getDuree(),
            "immat" => $contrat->getVehicule()->getImmatriculation(),
            "energie" => $contrat->getVehicule()->getEnergie(),
            "num_serie" => $contrat->getVehicule()->getSerie(),
            "type_mine" => $contrat->getVehicule()->getMine(),
            "dt_deb_garantie" => $datesDossier['dateGarantie'],
            "total_ttc_mensuel" => $contrat->getGarantie()->getMtTTC() / $contrat->getGarantie()->getDuree(),
            "reg_iban" => $prelevement ?$contrat->getPrelevement()->getIban() :"",
            "reg_bic" => $prelevement ?$contrat->getPrelevement()->getBic() :"",
            "banque" => "",
            "date_naissance" => "",
            "reg_nom" =>$prelevement ? $contrat->getPrelevement()->getNomPrenom() : "",
            "reg_adresse" => $prelevement ? $contrat->getPrelevement()->getAdresse() :"",
            "reg_cp" =>$prelevement ? $contrat->getPrelevement()->getCp() :"",
            "reg_ville" =>$prelevement ? $contrat->getPrelevement()->getVille() :"",
        );

        return json_encode($jsonContrat);
    }

    //Génératon du JSON pour l'espace client
    public function generateContractForClientWorkspace($contrat,$moyenPaiement){

        $jsonEspc = array(
            "compte" => array(
                "login" => $contrat->getClient()->getEmail(),
                "password" => "tempo",
            ),
            "beneficiaire" => array(
                "civilite" => $contrat->getClient()->getCivilite(),
                "nom" => $contrat->getClient()->getNom(),
                "prenom" => $contrat->getClient()->getPrenom(),
                "adresse" => $contrat->getClient()->getAdresse(),
                "adresse_suite" => "",
                "code_postal" => $contrat->getClient()->getCodePostal(),
                "ville" => $contrat->getClient()->getVille(),
                "telephone" => $contrat->getClient()->getTelephone(),
                "tel_bureau" => "",
                "mobile" => "",
                "email" => $contrat->getClient()->getEmail(),
            ),
            "vehicule" => array(
                "marque" => $contrat->getVehicule()->getMarque(),
                "modele" => $contrat->getVehicule()->getModele(),
                "immat" => $contrat->getVehicule()->getImmatriculation(),
                "km" => $contrat->getVehicule()->getKilometrage(),
                "date_circulation" => $contrat->getVehicule()->getDate(),
                "pfisc" => "",
                "energie" => $contrat->getVehicule()->getEnergie(),
                "valeur_neuf" => $contrat->getVehicule()->getPrix(),
                "type_mine" => $contrat->getVehicule()->getMine(),
                "num_serie" => $contrat->getVehicule()->getSerie(),
            ),
            "garantie" => array(
                "numero" =>  $contrat->getNumeroContrat(),
                "gcode" => 10000,
                "article" => $contrat->getGarantie()->getCode(),
                "duree" => $contrat->getGarantie()->getDuree(),
                "montant_ht" => $contrat->getGarantie()->getMtHT(),
                "montant_ttc" => $contrat->getGarantie()->getMtTTC(),
                "date_debut" => $contrat->getGarantie()->getDateDebutFormat(),
                "date_fin" => $contrat->getGarantie()->getDateFin(),
                "date_souscription" => date_format(new \DateTime, "Ymd"),
                "moyen_paiement" => $moyenPaiement,
            ),
            "documents" => array(
                "doc_cg" => "",
                "doc_cp" => "",
                "doc_echeancier" => "",
            )
        );

        return $jsonEspc;
    }

    //Génération de l'espace client
    public function sendDataToClientWorkspace($body){
        $response = $this->client->request(
            'POST',
            $this->param->get('app.wsdv_url') . 'espace/client',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')],
                'body' => json_encode($body)
            ]
        );
        return $response;
    }
    public function resetPassword($body){
        $response = $this->client->request(
            'POST',
            $this->param->get('app.wsdv_url') . '/espace/lbg/password/reinitialisation',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')],
                'body' => $body
            ]
        );
        return $response;
    }
    function choicePaiementMode($modeReglement){
        switch ($modeReglement) {
            case "mensuel":
                return "Règlement mensuel";
            case "3fois":
                return "Règlement en 3 fois";
            case "1fois":
                return "Règlement en 1 fois";
            case "1foiscb":
                return "Règlement en 1 fois par carte bancaire";
        }
    }
    public function sendEmail($emailSender,$emailReceiver,$mailer,$contentMessage,$view){
        if (filter_var($emailReceiver, FILTER_VALIDATE_EMAIL) == true) {
            $email = (new Email())
                ->embed(fopen('../assets/img/logo.png', 'r'), 'logo')
                ->from($emailSender)
                ->to($emailReceiver)
                ->subject($contentMessage)
                ->html($view->getContent());
                 $mailer->send($email);

        }
    }
    public function checkMailExist($emailSender,$emailReceiver,$mailer,$contentMessage,$view){
        try{
            if (filter_var($emailReceiver, FILTER_VALIDATE_EMAIL) == true) {
                $email = (new Email())
                    ->embed(fopen('../assets/img/logo.png', 'r'), 'logo')
                    ->from($emailSender)
                    ->to($emailReceiver)
                    ->subject($contentMessage)
                    ->html($view->getContent());
                $mailer->send($email);
              return true;
            }
            else{
                return false;
            }
        }
        catch (TransportExceptionInterface $e) {
         return false;
        }
    }


}