<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{

    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentions_legales(): Response
    {

        return $this->render('pages/mention/mention.html.twig', []);
    }
    #[Route('/faq', name: 'app_faq')]
    public function faq(): Response
    {

        return $this->render('pages/faq/faq.html.twig', []);
    }
    #[Route('/a-propos', name: 'app_apropos')]
    public function apropos(): Response
    {

        return $this->render('pages/apropos/apropos.html.twig', []);
    }
    #[Route('/expertise', name: 'app_expertise')]
    public function expertise(): Response
    {

        return $this->render('pages/expertise/expertise.html.twig', []);
    }
    #[Route('/en-cas-de-panne', name: 'app_encasdepanne')]
    public function enCasDePanne(): Response
    {

        return $this->render('pages/encasdepanne/encasdepanne.html.twig', []);
    }
    #[Route('/offres', name: 'app_offres')]
    public function offres(): Response
    {

        return $this->render('pages/offres/offres.html.twig', []);
    }

    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('pages/cgu/cgu.html.twig', []);
    }
    #[Route('/donnee', name: 'donnee')]
    public function donnee(): Response
    {
        return $this->render('pages/donnee/donnee.html.twig', []);
    }
    #[Route('/info', name: 'info')]
    public function info(): Response
    {
        return $this->render('pages/info/info.html.twig', []);
    }
    #[Route('/mention', name: 'mention')]
    public function mention(): Response
    {
        return $this->render('pages/mention/mention.html.twig', []);
    }

    #[Route('/offres/zen', name: 'app_offres_zen')]
    public function zen(): Response
    {
        return $this->render('pages/offres/offre-zen.html.twig', []);
    }

    #[Route('/offres/premium', name: 'app_offres_premium')]
    public function premium(): Response
    {
        return $this->render('pages/offres/offre-premium.html.twig', []);
    }

    #[Route('/offres/sweet', name: 'app_offres_sweet')]
    public function sweet(): Response
    {

        return $this->render('pages/offres/offre-sweet.html.twig', []);
    }

    #[Route('/offres/pieces/sweet', name: 'app_offres_pieces_sweet')]
    public function offresPiecesSweet(): Response
    {
        return $this->render('pages/offres/pieces/pieces-sweet.html.twig', []);
    }

    #[Route('/offres/pieces/premium', name: 'app_offres_pieces_premium')]
    public function offresPiecesPremium(): Response
    {
        return $this->render('pages/offres/pieces/pieces-premium.html.twig', []);
    }

    #[Route('/offres/pieces/zen', name: 'app_offres_pieces_zen')]
    public function offresPiecesZen(): Response
    {
        return $this->render('pages/offres/pieces/pieces-zen.html.twig', []);
    }

    #[Route('/offres/pieces/all/alimentation', name: 'app_offre_pieces_all_alimentation')]
    public function offresPiecesAllAlimentation(): Response
    {
        return $this->render('pages/offres/pieces/all/alimentation.html.twig', []);
    }

    #[Route('/offres/pieces/all/boite-vitesse-automatique', name: 'app_offre_pieces_all_boite_automatique')]
    public function offresPiecesAllBoiteAutomatique(): Response
    {
        return $this->render('pages/offres/pieces/all/boite-vitesse-automatique.html.twig', []);
    }

    #[Route('/offres/pieces/all/boite-vitesse-manuelle', name: 'app_offre_pieces_all_boite_manuelle')]
    public function offresPiecesAllBoiteManuelle(): Response
    {
        return $this->render('pages/offres/pieces/all/boite-vitesse-manuelle.html.twig', []);
    }

    #[Route('/offres/pieces/all/carter', name: 'app_offre_pieces_all_carter')]
    public function offresPiecesAllCarter(): Response
    {
        return $this->render('pages/offres/pieces/all/carter.html.twig', []);
    }

    #[Route('/offres/pieces/all/circuit-electrique-electronique', name: 'app_offre_pieces_all_circuit_electrique_electronique')]
    public function offresPiecesAllCircuitElectriqueElectronique(): Response
    {
        return $this->render('pages/offres/pieces/all/circuit-electrique-electronique.html.twig', []);
    }

    #[Route('/offres/pieces/all/circuit-refroidissement', name: 'app_offre_pieces_all_circuit_refroidissement')]
    public function offresPiecesAllCircuitRefroidissement(): Response
    {
        return $this->render('pages/offres/pieces/all/circuit-refroidissement.html.twig', []);
    }

    #[Route('/offres/pieces/all/climatisation', name: 'app_offre_pieces_all_climatisation')]
    public function offresPiecesAllClimatisation(): Response
    {
        return $this->render('pages/offres/pieces/all/climatisation.html.twig', []);
    }

    #[Route('/offres/pieces/all/direction', name: 'app_offre_pieces_all_direction')]
    public function offresPiecesAllDirection(): Response
    {
        return $this->render('pages/offres/pieces/all/direction.html.twig', []);
    }

    #[Route('/offres/pieces/all/embrayage', name: 'app_offre_pieces_all_embrayage')]
    public function offresPiecesAllEmbrayage(): Response
    {
        return $this->render('pages/offres/pieces/all/embrayage.html.twig', []);
    }

    #[Route('/offres/pieces/all/freinage', name: 'app_offre_pieces_all_freinage')]
    public function offresPiecesAllFreinage(): Response
    {
        return $this->render('pages/offres/pieces/all/freinage.html.twig', []);
    }

    #[Route('/offres/pieces/all/moteur', name: 'app_offre_pieces_all_moteur')]
    public function offresPiecesAllMoteur(): Response
    {
        return $this->render('pages/offres/pieces/all/moteur.html.twig', []);
    }

    #[Route('/offres/pieces/all/securite', name: 'app_offre_pieces_all_securite')]
    public function offresPiecesAllSecurite(): Response
    {
        return $this->render('pages/offres/pieces/all/securite.html.twig', []);
    }

    #[Route('/offres/pieces/all/suspension', name: 'app_offre_pieces_all_suspension')]
    public function offresPiecesAllSuspension(): Response
    {
        return $this->render('pages/offres/pieces/all/suspension.html.twig', []);
    }

    #[Route('/offres/pieces/all/transmission', name: 'app_offre_pieces_all_transmission')]
    public function offresPiecesAllTransmission(): Response
    {
        return $this->render('pages/offres/pieces/all/transmission.html.twig', []);
    }

   
}
