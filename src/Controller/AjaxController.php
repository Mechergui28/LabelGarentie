<?php

namespace App\Controller;

use App\Security\UserProvider;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WebService;
use Symfony\Component\Mime\Email;

class AjaxController extends AbstractController
{

    private $wsdv;
    private $session;
    private $param;

    public function __construct(WebService $wsdv, RequestStack $requestStack,ParameterBagInterface $param) {
        $this->wsdv = $wsdv;
        $this->session = $requestStack->getSession();
        $this->param=$param;

    }

    #[Route('/ajax/modele/{marque}', name: 'app_ajax_modele')]
    public function ajax_modele(Request $request): JsonResponse {

        $liste_modele = array();

        if ('GET' == $request->getMethod() && $request->isXmlHttpRequest()) {
            $liste_modele = $this->wsdv->getModeles($request->get('marque'));
        }

        return new JsonResponse($liste_modele);

    }
    #[Route('/ajax/sinistres/actualite', name: 'app_ajax_sinistres_actualite')]
    public function ajax_sinistres_actualite(Request $request): JsonResponse {

        $liste_modele = array();

        if ('GET' == $request->getMethod() && $request->isXmlHttpRequest()) {
            $liste_modele = $this->wsdv->getSinitres();
        }

        return new JsonResponse($liste_modele);

    }
    #[Route('/ajax/immat/verif', name: 'app_ajax_immat_vehicule')]
    public function getVehicule(Request $request){
        if ($request->isXmlHttpRequest()) {
            //$energies = array("ELECTRIC", "ELEC+ESSENC", "ELEC+GAZOLE", "ELEC+G.P.L.","ESS+ELEC HNR","GAZ+ELEC HNR","ELEC+G.NAT","GAZ+ELEC HR","ESS+ELEC HR");
            $energies = array("ELECTRIC");
            $immat= $request->get('immat');
            $vehicule = $this->wsdv->findByImmatriculation($immat);
            if(in_array($vehicule->getEnergie(), $energies)){
                $response = json_encode(array('response' => "Erreur"));
                return new JsonResponse($response);
            }
            else{
                $response = json_encode(array('response' => "OK"));
                return new JsonResponse($response);
            }

        }
        $response = json_encode(array('response' => "Erreur"));
        return new JsonResponse($response);
    }
    #[Route('/pwd', name: 'app_pwd',methods: ['POST'])]
    public function pwd(Request $request,UserProvider $userProvider,MailerInterface $mailer){
        $username = $request->request->get('email');
        $user=$userProvider->loadUserByIdentifier($username);
        if ($request->isXmlHttpRequest()) {
            if($user){
                $body=array('email'=>$username);
                $body=json_encode($body);
                $res=$this->wsdv->reinitPassword($body);
                if($res->getStatusCode() == 200){
                    $response = json_encode(array('response' => "OK"));
                    return new JsonResponse($response);
                }
                else{
                    $response = json_encode(array('response' => "Vérifier le mail"));
                    return new JsonResponse($response);
                }




            }
            else{
                $response = json_encode(array('response' => "Erreur"));
                return new JsonResponse($response);
            }

        }
        $response = json_encode(array('response' => "Erreur"));
        return new JsonResponse($response);

    }
    #[Route('/ajax/modele/set/{modele}', name: 'app_ajax_set_modele')]
    public function setModele(Request $request){
        if ('GET' == $request->getMethod() && $request->isXmlHttpRequest()) {
            $this->session->set('modele',$request->get('modele'));
        }
        return new  JsonResponse($request->get('modele'));
    }
    #[Route('/ajax/check/date', name: 'app_ajax_check_date',methods: ['POST'])]
    public function check_date(Request $request){
        if($request->isXmlHttpRequest()){
            $day=(int)$request->request->get('day');
            $month=(int)$request->request->get('month');
            $year=(int)$request->request->get('year');
            $dateDebutContrat=$day.'/'.$month.'/'.$year;
            $checkDate = date_format(\DateTime::createFromFormat('d/m/Y',$dateDebutContrat), "Y-m-d");
            $date = date_format(new \DateTime('now'), "Y-m-d");
            if((checkdate($month,$day,$year)) && (strtotime($date) <= strtotime($checkDate))){
                $response = json_encode(array('response' => "OK"));
                return new JsonResponse($response);
            }
            else{
                $response = json_encode(array('response' => "Erreur"));
                return new JsonResponse($response);
            }
        }
        else{
            $response = json_encode(array('response' => "Erreur"));
            return new JsonResponse($response);
        }
    }

    #[Route('/contact', name: 'app_ajax_send_contact',methods:['POST'])]
    public function SendContact(Request $request, MailerInterface $mailer): JsonResponse
    
    {
        if ($request->isXmlHttpRequest()) {

                    $message =
                        "nom : " . $request->get('name') .
                        "\n\remail : " . $request->get('email') .
                        "\n\rtel : " . $request->get('tel') .
                        "\n\rimmat : " . $request->get('immat') .
                        "\n\rtexte : " . $request->get('message');
        
                    $email = $request->get('email');
        
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                        $response = json_encode(array('response' => "Erreur"));
                        return new JsonResponse($response);
                    }
        
                    $email = (new Email())
                        ->from('contact@labelgarantie.com')
                        ->to('contact@labelgarantie.com')
                        ->subject('La belle garantie : Fomulaire contact')
                        ->text($message);
        
        
                    $mailer->send($email);
        
                    $response = json_encode(array('response' => 'Ok'));
                    return new JsonResponse($response);
                }
       
    }

    #[Route('/iban', name: 'app_ajax_iban')]
    public function controle_iban(Request $request): JsonResponse
    {

        // Tableau de correspondance Alpha vers num
        $m_tab_Alpha["A"]        = 10;
        $m_tab_Alpha["B"]        = 11;
        $m_tab_Alpha["C"]        = 12;
        $m_tab_Alpha["D"]        = 13;
        $m_tab_Alpha["E"]        = 14;
        $m_tab_Alpha["F"]        = 15;
        $m_tab_Alpha["G"]        = 16;
        $m_tab_Alpha["H"]        = 17;
        $m_tab_Alpha["I"]        = 18;
        $m_tab_Alpha["J"]        = 19;
        $m_tab_Alpha["K"]        = 20;
        $m_tab_Alpha["L"]        = 21;
        $m_tab_Alpha["M"]        = 22;
        $m_tab_Alpha["N"]        = 23;
        $m_tab_Alpha["O"]        = 24;
        $m_tab_Alpha["P"]        = 25;
        $m_tab_Alpha["Q"]        = 26;
        $m_tab_Alpha["R"]        = 27;
        $m_tab_Alpha["S"]        = 28;
        $m_tab_Alpha["T"]        = 29;
        $m_tab_Alpha["U"]        = 30;
        $m_tab_Alpha["V"]        = 31;
        $m_tab_Alpha["W"]        = 32;
        $m_tab_Alpha["X"]        = 33;
        $m_tab_Alpha["Y"]        = 34;
        $m_tab_Alpha["Z"]        = 35;

        if ($request->isXmlHttpRequest()) {

            $iban = $request->get('iban');

            //On nettoie l'iban
            $iban_temp    = $iban;
            $iban_temp    = str_replace(" ", "", $iban_temp);
            $iban_temp    = str_replace("-", "", $iban_temp);

            //On récupère les quatres premiers caractère
            $iban_4premiers = substr($iban_temp, 0, 4);

            //On récupère tout le reste
            $iban_all = substr($iban_temp, 4, strlen($iban_temp));

            $controle_iban = "";

            for ($i = 0; $i <= strlen($iban_all); $i++) {

                $caract = substr($iban_all, $i, 1);
                if (ctype_alpha($caract)) {
                    $controle_iban .= $m_tab_Alpha[$caract];
                } else {
                    $controle_iban .= $caract;
                }
            }

            for ($i = 0; $i <= strlen($iban_4premiers); $i++) {

                $caract = substr($iban_4premiers, $i, 1);
                if (ctype_alpha($caract)) {
                    $controle_iban .= $m_tab_Alpha[$caract];
                } else {
                    $controle_iban .= $caract;
                }
            }

            if (bcmod($controle_iban, '97') == 1) {

                $response = json_encode(array('response' => 'Ok'));
                return new JsonResponse($response);
            } else {

                $response = json_encode(array('response' => 'IBAN erroné'));
                return new JsonResponse($response);
            }
        } else {

            $response = json_encode(array('response' => 'Erreur'));
            return new JsonResponse($response);
        }
    }
    #[Route('/bic', name: 'app_ajax_bic')]
    public function controle_bic(Request $request): JsonResponse
    {

        $tabBic = [
            "ABNAFRPP",
            "AECFFR21",
            "AFRIFRPP",
            "AGFBFRCC",
            "AGRIFRPI",
            "AGRIFRPP",
            "AGRIMQMX",
            "AGRIRERX",
            "ARCEFRP1",
            "AUDIFRPP",
            "AXABFRPP",
            "BAMYFR22",
            "BARCFRPP",
            "BATIFRP1",
            "BBPIFRPP",
            "BCDMFRPP",
            "BCHAFR21",
            "BCITFRPP",
            "BCMAFRPP",
            "BCRTFRP1",
            "BCRTFRPP",
            "BDEIFRPP",
            "BDFEFR2L",
            "BDFEFR2T",
            "BDFEFRPP",
            "BDUPFR2S",
            "BEPOFR21",
            "BFBKFRP1",
            "BGFIFRPP",
            "BIKCFRP1",
            "BMCEFRPP",
            "BMMMFR2A",
            "BMRZFR21",
            "BNABFRPP",
            "BNPAFRPH",
            "BNPAFRPP",
            "BNPAGFGX",
            "BNPARERX",
            "BNPCFR21",
            "BNUGFR21",
            "BOFAFRPP",
            "BOTKFRPX",
            "BOUSFRPP",
            "BPCEFRPP",
            "BPFIFRP1",
            "BPNPFRP1",
            "BPOLPFTP",
            "BPPBFRP1",
            "BPSMFRPP",
            "BRASFRPP",
            "BREDFRPP",
            "BSAVFR2C",
            "BSPFFRPP",
            "BSUIFRPP",
            "CAONFR21",
            "CCBPFRPP",
            "CCFEFRP1",
            "CCFRFRCR",
            "CCFRFRPP",
            "CCMOFR21",
            "CCMVFR21",
            "CCOPFRCP",
            "CCOPFRPP",
            "CCUTFR21",
            "CDCGFRPP",
            "CDPRFRP1",
            "CEPAFRPP",
            "CEPANCNM",
            "CFCUFR21",
            "CFFIFR2L",
            "CFFRFRPP",
            "CGCPFRP1",
            "CGDIFRPP",
            "CGRCFRP1",
            "CHASFRPB",
            "CHASFRPP",
            "CITIFRPP",
            "CLBQFRP1",
            "CMBRFR2B",
            "CMCIFR21",
            "CMCIFR2A",
            "CMCIFRP1",
            "CMCIFRPA",
            "CMCIFRPB",
            "CMCIFRPP",
            "CMDIFR21",
            "CMMMFR21",
            "CMUTFR21",
            "COBAFRPX",
            "CONOFRP1",
            "COUOFR21",
            "COURFR2T",
            "CPMEFRPP",
            "CRAFFRP1",
            "CRGEFR2X",
            "CRLYFRPP",
            "CRTAFR21",
            "DELUFR22",
            "DEUTFRPP",
            "ECOCFRPP",
            "EDFGFRPP",
            "ELFAFRP1",
            "FACFFRPP",
            "FAMSFRPP",
            "FEMBFRP1",
            "FIDCFR21",
            "FPELFR21",
            "FTELFRPP",
            "FTNOFRP1",
            "GPBAFRPP",
            "GSCFFR22",
            "GSFHFRPP",
            "GSZGFRPP",
            "HCREFR21",
            "IIDFFR21",
            "INGBFRPP",
            "ISAEFRPP",
            "KOEXFRPP",
            "KOLBFR21",
            "KREDFRPP",
            "LAYDFR2W",
            "LCLPFRP1",
            "LEGRFRP1",
            "MCCFFRP1",
            "MIDLFRCP",
            "MIDLFRPX",
            "MONTFRPP",
            "NATXFRPP",
            "NFACFR21",
            "NORDFRPP",
            "NSMBFRPP",
            "ODDOFRPP",
            "ODYVFRP1",
            "OPSPFR21",
            "PARBFRPP",
            "PAREFRPP",
            "POUYFR21",
            "PREUFRP1",
            "PSABFRPP",
            "PSSTFRPP",
            "RALPFR2G",
            "REUBRERX",
            "RGFIFRPP",
            "RGFPFRP1",
            "SBEXFRP1",
            "SBINFRPP",
            "SFBSFRP1",
            "SICVFRPP",
            "SLMPFRP1",
            "SMCTFR2A",
            "SOAPFR22",
            "SOCBPFTX",
            "SOGEFRPP",
            "SOGEGPGP",
            "SOGENCNN",
            "SORMFR2N",
            "TARNFR2L",
            "TRGLFRP1",
            "TRPUFRP1",
            "UBAFFRPP",
            "USINFRPP",
            "YROCFRPP",
            "BIC75650"
        ];

        if ($request->isXmlHttpRequest()) {

            $bic = $request->get('bic');

            if (strlen($bic) != 8 && strlen($bic) != 11) {
                $response = json_encode(array('response' => 'Pas la taille requise'));
                return new JsonResponse($response);
            }

            if (in_array(substr($bic, 0, 8), $tabBic) == false) {
                $response = json_encode(array('response' => "N'est pas un BIC connu"));
                return new JsonResponse($response);
            }

            $response = json_encode(array('response' => 'Ok'));
            return new JsonResponse($response);
        } else {

            $response = json_encode(array('response' => 'Erreur lors de la requête'));
            return new JsonResponse($response);
        }
    }

    #[Route('/ajax/change/date', name: 'app_ajax_change_date',methods: ['POST'])]
    public function change_date(Request $request){
        if($request->isXmlHttpRequest()){
            $day=(int)$request->request->get('day');
            $month=(int)$request->request->get('month');
            $year=(int)$request->request->get('year');
            $dateDebutContrat=$day.'/'.$month.'/'.$year;
            $checkDate = date_format(\DateTime::createFromFormat('d/m/Y',$dateDebutContrat), "Y-m-d");
            $date = date_format(new \DateTime('now'), "Y-m-d");
            if((checkdate($month,$day,$year)) && (strtotime($date) <= strtotime($checkDate))){
                
                $contrat = $this->session->get("contrat");
                $contrat->getGarantie()->setDateDebut(date_format(\DateTime::createFromFormat('d/m/Y',$dateDebutContrat), "d/m/Y"));
                $this->session->set("contrat", $contrat);

                $response = json_encode(array('response' => "ok"));
                return new JsonResponse($response);
            }
            else{
                $response = json_encode(array('response' => "Erreur"));
                return new JsonResponse($response);
            }
        }
        else{
            $response = json_encode(array('response' => "Erreur"));
            return new JsonResponse($response);
        }
    }


}