<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Signataire;
use App\Entity\Contrat;
use App\Service\PdfService;
use Twig\Environment;

class SignRequestService
{

    private $param;
    private $client;
    private $twig;
    private $pdfService;

    public function __construct(Environment $twig, ParameterBagInterface $params, HttpClientInterface $client, PdfService $pdfService)
    {
        $this->twig = $twig;
        $this->param = $params;
        $this->client = $client;
        $this->pdfService = $pdfService;
    }

    public function generateSignature(Contrat $contrat)
    {
        //Génération du document
        $file = $this->pdfService->pdfSignature($contrat);
        if ($file == "" || file_exists($file) === false) {
            return false;
        }

        //Génération du signataire
        $signataire = new Signataire();
        $signataire->setNom($contrat->getClient()->getNom());
        $signataire->setPrenom($contrat->getClient()->getPrenom());
        $signataire->setEmail($contrat->getClient()->getEmail());
        $signataire->setLangue("fr");

        //Génération du texte
        $sujet = "Signature électronique LA BELLE GARANTIE";
        $message = $this->twig->render('signature/signature.html.twig', [
            'civilite' => $contrat->getClient()->getCivilite(),
            'nom' => $contrat->getClient()->getNom(),
            'produit' => $contrat->getGarantie()->getLibelle(),
            'marque' => $contrat->getVehicule()->getMarque(),
            'modele' => $contrat->getVehicule()->getModele(),
            'immatriculation' => $contrat->getVehicule()->getImmatriculation(),
            'duree' => $contrat->getGarantie()->getDuree(),
            'ttc' => $contrat->getGarantie()->getMtTTC()
        ]);

        //Envoi de la signature
        return $this->sendSignature($file, $sujet, $message, $signataire);
    }

    public function sendSignature(string $doc, string $sujet, string $message, Signataire $signataire)
    {
        try {
            $file = base64_encode(file_get_contents($doc));
            $body = [
                'from_email' => $this->param->get('app.email'),
                'from_email_name' => $this->param->get('app.email'),
                'subject' => $sujet,
                'message' => $message,
                'who' => 'o',
                'signers' => [
                    array(
                        'email' => $signataire->getEmail(),
                        'first_name' => $signataire->getPrenom(),
                        'last_name' => $signataire->getNom(),
                        'language' => $signataire->getLangue(),
                        'force_language' => 'true'
                    )
                ],
                'file_from_content' => $file,
                'file_from_content_name' => 'Contrat.pdf',
                'redirect_url' => 'https://www.labellegarantie.com/'
            ];

            $header = [
                'Authorization' => 'Token ' . $this->param->get('app.signrequest_token'),
                'Content-Type' => 'application/json'
            ];

            //Appel du webservice immatriculation
            $response = $this->client->request(
                'POST',
                $this->param->get('app.signrequest_url') . 'signrequest-quick-create/',
                [
                    'headers' => $header,
                    'json' => $body,
                ]
            );
        } catch (\Exception $e) {
            return false;
        }

        if ($response->getStatusCode() === 201) {
            return true;
        } else {
            return false;
        }
    }
}
