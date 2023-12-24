<?php

namespace App\Shared;

class Shared
{
    public function __construct()
    {
    }
    const DETAILS_SWEET=[
        'infos'=>[
            0=> "Moteur",
            1=>"Système d’alimentation",
            2=>"Boîte de vitesses",
            3=>"Dispositif d’embrayage",
            4=>"Boîte de vitesses automatique",
            5=>"Circuit électrique",
            6=>"Pont",
            7=>"Carter",
            8=>"Freinage",
            9=>"Compresseur de climatisation",
            10=>"Alimentation",
            11=>"Direction",
            12=>"Suspension",
            13=>"Ingrédients",
            14=>"Diagnostic"
        ],
        'image'=>'build/img/sweet1.webp',
        'title'=> 'SWEET',
        'subTitle' => '+ de 50 pièces couvertes',
        'file'=>'offres/pieces/sweet',
        'desc' => ['text'=>'La plus douce','class'=>'sweet'],
        'ipid' => 'build/docs/sweet/ipid_lbg_sweet.pdf',
        'cg' => 'build/docs/sweet/cg_lbg_sweet.pdf',
        'cp' => 'build/docs/sweet/cp_lbg_sweet.pdf'
    ];
    const DETAILS_ZEN=[
        'infos'=>[
            0=> "Moteur",
            1=>"Système d’alimentation",
            2=>"Boîte de vitesses",
            3=>"Dispositif d’embrayage",
            4=>"Boîte de vitesses automatique",
            5=>"Circuit électrique",
            6=>"Circuit électronique",
            7=>"Climatisation",
            8=>"Direction",
            9=>"Freinage",
            10=>"Circuit de refroidissement",
            11=>"Sécurité",
            12=>"Transmissions/Pont",
            13=>"Suspension",
            16=>"Ingrédients",
            17=>"Diagnostic"
        ],
        'image'=>'build/img/zen2.webp',
        'title'=> 'ZEN',
        'subTitle' => '+ de 100 pièces couvertes',
        'file'=>'/offres/pieces/zen',
        'desc' => ['text'=>'La plus cool','class'=>'zen'],
        'ipid' => 'build/docs/sweet/ipid_lbg_zen.pdf',
        'cg' => 'build/docs/zen/cg_lbg_zen.pdf',
        'cp' => 'build/docs/zen/cp_lbg_zen.pdf'
    ];
    const DETAILS_PREMIUM=[
        'infos'=>[
            0=> "Moteur",
            1=>"Système d’alimentation",
            2=>"Boîte de vitesses",
            3=>"Dispositif d’embrayage",
            4=>"Boîte de vitesses automatique",
            5=>"Circuit électrique",
            6=>"Circuit électronique",
            7=>"Climatisation",
            8=>"Direction",
            9=>"Freinage",
            10=>"Circuit de refroidissement",
            11=>"Sécurité",
            12=>"Transmissions/Pont",
            13=>"Suspension",
            14=>"Vanne EGR",
            15=>"Organes de confort",
            16=>"Ingrédients",
            17=>"Diagnostic"
        ],
        'image'=>'build/img/premium1.webp',
        'title'=> 'PREMIUM',
        'subTitle' => '+ de 250 pièces couvertes',
        'file'=>'/offres/pieces/premium',
        'desc' => ['text'=>'La plus belle','class'=>'premium'],
        'ipid' => 'build/docs/premium/ipid_lbg_premium.pdf',
        'cg' => 'build/docs/premium/cg_lbg_premium.pdf',
        'cp' => 'build/docs/premium/cp_lbg_premium.pdf'

    ];
}