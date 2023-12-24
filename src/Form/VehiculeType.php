<?php

namespace App\Form;

use App\Entity\Pannier;
use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('modele', ChoiceType::class, [
                'placeholder' => 'Sélectionnez le modèle*',
                'choices' => null,
            ])


            ->add('immatriculation', TextType::class)
            ->add('kilometrage', TextType::class)
            ->add('prix', TextType::class)

            ->add('energie', ChoiceType::class, [
                'placeholder' => 'Sélectionnez le carburant*',
                'choices' => [
                    'Essence' => 'ESSENCE',
                    'Essence + gazogène' => 'ESS+GAZO',
                    'Essence + G.P.L.' => 'ESS+G.P.L.',
                    'Essence + gaz naturel comprimé (EN)' => 'ESS+G.NAT',
                    'Gazole' => 'GAZOLE',
                    'Fuel-Oil' => 'FUEL-OIL',
                    'Gazole + Gazogène' => 'GAZOLE+GAZO',
                    'Gazogène' => 'GAZOGENE',
                    'Carburant gazeux' => 'CARB.GAZEUX',
                    'Gaz naturel véhicule' => 'GAZ NAT.VEH',
                    'Electricité' => 'ELECTRIC',
                    'Gaz de pétrole liquéfié' => 'G.P.L.',
                    'Pétrole lampant' => 'PETROL.LAMP',
                    'Electricité-Essence' => 'ELEC+ESSENC',
                    'Electricité-Gazole' => 'ELEC+GAZOLE',
                    'Air comprimé' => 'AIR COMPRIM',
                    'Hydrogène' => 'HYDROGENE',
                    'Electricité-Monocarburation G.P.L.' => 'ELEC+G.P.L.',
                    'Essence-Electricité Hybride Non-Rechargeable' => 'ESS+ELEC HNR',
                    'Gaz-Electricité Hybride Non-Rechargeable' => 'GAZ+ELEC HNR',
                    'Essence-Electricité Hybride Rechargeable' => 'ESS+ELEC HR',
                    'Gaz-Electricité Hybride Rechargeable' => 'GAZ+ELEC HR',
                    'Electricité-Gaz Naturel' => 'ELEC+G.NAT',
                    'Superethanol' => 'SUPERETHANOL',
                    'Energie Inconnue' => 'INCONNUE',
                    'Autre énergie' => 'AUTRES',
                    'Essence + Autre énergie' => 'BICARBUR',
                ]
            ])

            ->add('date', DateType::class, ['widget' => 'single_text'])
            ->add('serie', TextType::class, ['required' => false])
            ->add('mine', TextType::class, ['required' => false])
            ->add('calculer', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
    }
