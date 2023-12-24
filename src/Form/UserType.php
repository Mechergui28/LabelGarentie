<?php

namespace App\Form;

use App\Entity\UserCreation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class UserType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civilite', ChoiceType::class, [
                'placeholder' => 'Civilité*',
                'choices' => [
                    'Monsieur' => 'M',
                    'Madame' => 'MME'
                ]
            ])
            ->add('nom', TextType::class, array('label' => false))
            ->add('prenom', TextType::class, array('label' => false))
            ->add('adresse', TextType::class, array('label' => false))
            ->add('codePostal', TextType::class, array('label' => false))
            ->add('ville', TextType::class, array('label' => false))
            ->add('telephone', TextType::class, array('label' => false))
            ->add('accepte_mail', CheckBoxType::class, array('label' => false, 'required' => false))
        ;

        //Data transformer
        $builder->get('accepte_mail')
            ->addModelTransformer(new CallBackTransformer(
                function($accepte_mail){
                    return $accepte_mail == "Oui" ? true : false;
                },
                function ($accepte_mail){
                    return $accepte_mail == true ? "Oui" : "Non";
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserCreation::class,
        ]);
    }
}