<?php

namespace App\Form;

use App\Entity\Prelevement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrelevementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                 ->add('iban', TextType::class, ['label' => false, 'required' => true])
                 ->add('bic', TextType::class, ['label' => false , 'required' => true])
                 ->add('nomPrenom', TextType::class, ['label' => false , 'required' => true])
                 ->add('adresse', TextType::class, ['label' => false , 'required' => true])
                 ->add('cp', TextType::class, ['label' => false , 'required' => true])
                 ->add('ville', TextType::class, ['label' => false , 'required' => true])
                 ->add('valider', SubmitType::class, ['label' => 'Reglement'])
             ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prelevement::class,
        ]);
    }
}