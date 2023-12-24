<?php

namespace App\Form;

use App\Entity\InfoGarantie;
use App\Entity\UserCreation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoGarantieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day', ChoiceType::class, [
                'placeholder' => 'Jours*',
                'choices' => [
                    '01' => '01',
                    '02' => '02',
                    '03' => '03',
                    '04' => '04',
                    '05' => '05',
                    '06' => '06',
                    '07' => '07',
                    '08' => '08',
                    '09' => '09',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12',
                    '13' => '13',
                    '14' => '14',
                    '15' => '15',
                    '16' => '16',
                    '17' => '17',
                    '18' => '18',
                    '19' => '19',
                    '20' => '20',
                    '21' => '21',
                    '22' => '22',
                    '23' => '23',
                    '24' => '24',
                    '25' => '25',
                    '26' => '26',
                    '27' => '27',
                    '28' => '28',
                    '29' => '29',
                    '30' => '30',
                    '31' => '31',

                ],
                'required' => true,

            ])
            ->add('month', ChoiceType::class, [
                'placeholder' => 'Mois*',
                'choices' => [
                     'Janvier'=>'01' ,
                     'Février'=>'02' ,
                     'Mars'=>'03' ,
                     'Avril'=>'04' ,
                     'Mai'=>'05' ,
                     'Juin'=>'06' ,
                     'Juillet'=>'07' ,
                     'Août'=>'08' ,
                     'Septembre'=>'09' ,
                     'Octobre'=>'10' ,
                     'Novembre'=>'11' ,
                     'Décembre'=>'12' ,

                ],
                'required' => true,

            ])
            ->add('year', ChoiceType::class, [
                'placeholder' => 'Année*',
                'choices' => [
                    '2022' => '2022',
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                    '2026' => '2026',
                    '2027' => '2027',
                ],
                'required' => true,
            ])
            ->add('ficheclient', CheckboxType::class, [
                'required' => true,
            ])
            ->add('ipid', CheckboxType::class, [
                'required' => true,
            ])
            ->add('valider', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoGarantie::class,
        ]);
    }
}