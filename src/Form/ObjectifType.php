<?php

namespace App\Form;

use App\Entity\Objectif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ObjectifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('descobj', TextType::class, [
            'label' => 'Type d\'objectif : ',
            'attr' => [
                'placeholder' => 'Saisissez l\'objectif du camping ',
                'class' => 'form-control', 
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir un type d\'objectif',
                ]),
                new Length([
                    'min' => 2,
                    'max' => 255,
                    'minMessage' => 'Le type d\'objectif doit contenir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le type d\'objectif ne doit pas dépasser {{ limit }} caractères',
                ]),
                
            ],
        ])
        
            ->add('resultatobj', TextType::class, [
                'label' => 'Resultat d\'objectif : ',
                'attr' => [
                    'placeholder' => 'Saisissez le resultat de l\'objectif du camping ',
                    'class' => 'form-control', 
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un résultat pour l\'objectif',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le résultat de l\'objectif doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le résultat de l\'objectif ne doit pas dépasser {{ limit }} caractères',
                    ]),
                    
                ],
            ])

            ->add('imageobj', FileType::class, [
                
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une image pour l\'objectif',
                    ]),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objectif::class,
        ]);
    }
}
