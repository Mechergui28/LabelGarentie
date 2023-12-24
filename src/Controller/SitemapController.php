<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap')]
    public function siteMap(Request $request): Response
    {

        $hostname = $request->getSchemeAndHttpHost();

        $urls = [];

        $urls[] = ['loc' => $this->generateUrl('app_index')];
        $urls[] = ['loc' => $this->generateUrl('info')];
        $urls[] = ['loc' => $this->generateUrl('cgu')];
        $urls[] = ['loc' => $this->generateUrl('donnee')];
        $urls[] = ['loc' => $this->generateUrl('app_offres')];
        $urls[] = ['loc' => $this->generateUrl('app_faq')];
        $urls[] = ['loc' => $this->generateUrl('app_infos_vehicule')];
        $urls[] = ['loc' => $this->generateUrl('mention')];
        $urls[] = ['loc' => $this->generateUrl('app_apropos')];
        $urls[] = ['loc' => $this->generateUrl('app_offres_sweet')];
        $urls[] = ['loc' => $this->generateUrl('app_offres_zen')];
        $urls[] = ['loc' => $this->generateUrl('app_offres_premium')];
        $urls[] = ['loc' => $this->generateUrl('app_offres_pieces_premium')];
        $urls[] = ['loc' => $this->generateUrl('app_offres_pieces_zen')];
        $urls[] = ['loc' => $this->generateUrl('app_offres_pieces_sweet')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_boite_automatique')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_direction')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_moteur')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_transmission')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_securite')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_boite_manuelle')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_suspension')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_embrayage')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_alimentation')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_circuit_refroidissement')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_climatisation')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_carter')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_circuit_electrique_electronique')];
        $urls[] = ['loc' => $this->generateUrl('app_offre_pieces_all_freinage')];

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname,
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');

        return $response;
    }

    #[Route('/robots.txt', name: 'app_robots')]
    public function robotsTxt(Request $request): Response
    {
        $hostname = $request->getSchemeAndHttpHost();

        $response = new Response(
            $this->renderView('sitemap/robots.html.twig', [
                'hostname' => $hostname
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/plain');

        return $response;
    }
}
