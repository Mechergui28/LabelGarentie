<?php

namespace App\Service;

use App\Entity\Contrat;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use setasign\Fpdi\Fpdi;
use Tomsgu\PdfMerger\PdfCollection;
use Tomsgu\PdfMerger\PdfFile;
use Tomsgu\PdfMerger\PdfMerger;
use Twig\Environment;

class PdfService
{
    const PATH_PDF = "../pdf/";
    const PATH_DOC = "../assets/docs/";

    private $twig;
    private $snappy;

    public function __construct(Environment $twig, Pdf $snappy)
    {
        $this->twig = $twig;
        $this->snappy = $snappy;
    }
    public function pdfBulletin(Contrat $contrat): string
    {

        $nameDoc = uniqid() . ".pdf";
        $path = \App\Service\PdfService::PATH_PDF . $nameDoc;

        $adresse = $contrat->getClient()->getAdresse() . ' ' . $contrat->getClient()->getCodePostal() . ' ' . $contrat->getClient()->getVille();
        try {
            $this->snappy->generateFromHtml(
                $this->twig->render(
                    'pdf/bulletin_adhesion.html.twig',
                    array(
                        'libelle'   => $contrat->getLibelle(),
                        'numero'   => $contrat->getNumeroContrat(),
                        'duree'     => $contrat->getGarantie()->getDuree(),
                        'dateDebut' => $contrat->getGarantie()->getDateDebut(),
                        'marque'    => $contrat->getVehicule()->getMarque(),
                        'modele'    => $contrat->getVehicule()->getModele(),
                        'date'      => $contrat->getVehicule()->getDate(),
                        'serie'     => $contrat->getVehicule()->getSerie(),
                        'immatriculation' => $contrat->getVehicule()->getImmatriculation(),
                        'mine' => $contrat->getVehicule()->getMine(),
                        'nom' => $contrat->getClient()->getNom() . ' ' . $contrat->getClient()->getPrenom(),
                        'telephone' => $contrat->getClient()->getTelephone(),
                        'adresse' => $adresse,
                        'moyenPaiement' => $contrat->getMoyenPaiement()
                    )
                ),
                $path,
                array(
                    'enable-javascript' => true,
                    'javascript-delay' => 1000,
                    'no-stop-slow-scripts' => true,
                    'no-background' => false,
                    'lowquality' => false,
                    'encoding' => 'utf-8',
                    'images' => true,
                    'cookie' => array(),
                    'dpi' => 300,
                    'enable-external-links' => true,
                    'enable-internal-links' => true
                )
            );
        } catch (\Exception $e) {
            return "";
        }

        if (file_exists($path)) {
            return $path;
        } else {
            return "";
        }
    }

    public function pdfMandat(Contrat $contrat)
    {

        $nameDoc = uniqid() . ".pdf";
        $path = pdfService::PATH_PDF . $nameDoc;

        try {

            $this->snappy->generateFromHtml(
                $this->twig->render(
                    'pdf/mandat_sepa.html.twig',
                    array(
                        'nom' => $contrat->getClient()->getNom(),
                        'prenom' => $contrat->getClient()->getPrenom(),
                        'adresse' => $contrat->getClient()->getAdresse(),
                        'codepostal' => $contrat->getClient()->getCodePostal(),
                        'ville' => $contrat->getClient()->getVille(),
                        'iban' => $contrat->getPrelevement()->getIban(),
                        'bic' => $contrat->getPrelevement()->getBic(),
                        'lieu' => $contrat->getClient()->getVille(),
                        'date' => new \DateTime('now'),
                        'typeReglement' => $contrat->getTypePaiement(),
                    )
                ),
                $path,
                array(
                    'enable-javascript' => true,
                    'javascript-delay' => 1000,
                    'no-stop-slow-scripts' => true,
                    'no-background' => false,
                    'lowquality' => false,
                    'encoding' => 'utf-8',
                    'images' => true,
                    'cookie' => array(),
                    'dpi' => 300,
                    'enable-external-links' => true,
                    'enable-internal-links' => true
                )
            );
        } catch (\Exception $e) {
            return "";
        }

        if (file_exists($path)) {
            return $path;
        } else {
            return "";
        }
    }

    public function pdfSignature(Contrat $contrat)
    {

        $path = pdfService::PATH_PDF . uniqid() . ".pdf";

        if ($contrat->getMoyenPaiement() != 'Paiement par carte bancaire') {

            $bulletin = $this->pdfBulletin($contrat);
            $mandat = $this->pdfMandat($contrat);
            try {

                $pdfCollection = new PdfCollection();
                //Condition général
                $pdfCollection->addPdf("../assets/docs/" . strtolower($contrat->getGarantie()->getLibelle()) . "/cg_lbg_" . strtolower($contrat->getGarantie()->getLibelle()) . ".pdf", PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);

                //IPID
                $pdfCollection->addPdf("../assets/docs/" . strtolower($contrat->getGarantie()->getLibelle()) . "/ipid_lbg_" . strtolower($contrat->getGarantie()->getLibelle()) . ".pdf", PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);

                //Bulletin et mandat
                if (file_exists($bulletin) != "" && file_exists($mandat)) {
                    $pdfCollection->addPdf($bulletin, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);
                    $pdfCollection->addPdf($mandat, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);

                    //Fusion des PDF
                    $fpdi = new Fpdi();
                    $merger = new PdfMerger($fpdi);
                    $merger->merge($pdfCollection, $path, PdfMerger::MODE_FILE, PdfFile::ORIENTATION_LANDSCAPE);

                    //Suppresion fichier temporaire
                    unlink($bulletin);
                    unlink($mandat);
                }
            } catch (\Exception $e) {
                return "";
            }
        } else {

            $bulletin = $this->pdfBulletin($contrat);

            try {

                $pdfCollection = new PdfCollection();
                //Condition général
                $pdfCollection->addPdf("../assets/docs/" . strtolower($contrat->getGarantie()->getLibelle()) . "/cg_lbg_" . strtolower($contrat->getGarantie()->getLibelle()) . ".pdf", PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);

                //IPID
                $pdfCollection->addPdf("../assets/docs/" . strtolower($contrat->getGarantie()->getLibelle()) . "/ipid_lbg_" . strtolower($contrat->getGarantie()->getLibelle()) . ".pdf", PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);

                //Bulletin et mandat
                if (file_exists($bulletin)) {
                    $pdfCollection->addPdf($bulletin, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);

                    //Fusion des PDF
                    $fpdi = new Fpdi();
                    $merger = new PdfMerger($fpdi);
                    $merger->merge($pdfCollection, $path, PdfMerger::MODE_FILE, PdfFile::ORIENTATION_LANDSCAPE);

                    //Suppresion fichier temporaire
                    unlink($bulletin);
                }
            } catch (\Exception $e) {
                dd($e);
                return "";
            }
        }

        if (file_exists($path)) {
            return $path;
        } else {
            return "";
        }
    }
    public function pdfFicheConseil(Contrat $contrat)
    {

        $html = $this->twig->render(
            'pdf/fiche_conseil.html.twig',
            array(
                'contrat' => $contrat,
            )
        );

        $footer = $this->twig->render('pdf/footer_fiche_conseil.html.twig');

        $this->snappy->setOption('footer-html', utf8_decode($footer));

        $response = new PdfResponse(
            $this->snappy->getOutputFromHtml($html, array(
                'margin-top'    => 10,
                'margin-right'  => 10,
                'margin-bottom' => 10,
                'margin-left'   => 10,
                'footer-spacing' => -5,
                'footer-font-name' => 'Calibri',
            )),
            'fiche conseil.pdf'
        );
        return $response;
    }
}
