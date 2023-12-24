<?php

namespace App\Form;

use App\Entity\Pannier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PannierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'prelevement',
                ChoiceType::class,
                [
                    'choices' => [
                        'Mensuel' => 'mensuel',
                        'En 3 fois' => '3fois',
                        'En 1 fois' => '1fois',
                        'Par Carte Bancaire' => '1foiscb'
                    ],
                    'expanded' => true,
                    'required' => true,
                    'data' => 'mensuel',
                ]
            )
            ->add('accepte', CheckboxType::class, [
                'required' => true,
            ])
            ->add('valider', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pannier::class,
        ]);
    }
}