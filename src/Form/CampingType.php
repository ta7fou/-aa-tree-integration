<?php

namespace App\Form;

use App\Entity\Camping;
use App\Entity\Objectif;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

class CampingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        
        $builder
            ->add('prixcamp', IntegerType::class, [
                'label' => 'Prix du camping',
                'attr' => ['placeholder' => 'Saisissez le prix du camping'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un prix pour le camping']),
                    new Type(['type' => 'integer', 'message' => 'Le prix doit être un nombre']),
                ],
            ])
            ->add('objid', EntityType::class, [
                'class' => Objectif::class,
                'choice_label' => 'descobj', // Assuming 'name' is a relevant property to display in Objectif
                'label' => 'Objectif',
            ])
            ->add('nbpmax', IntegerType::class, [
                'label' => 'Nombre de personnes maximal',
                'attr' => ['placeholder' => 'Saisissez le nombre max de personnes'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir le nombre max de personnes']),
                    new Type(['type' => 'integer', 'message' => 'Le nombre de personnes doit être un nombre']),
                ],
            ])
            ->add('datedebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une date de début']),
                ],
            ])
            
            ->add('datefin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une date de fin']),
                ],
            ])

            ->add('adressecamp', TextType::class, [
                'label' => 'Adresse du camping',
                'attr' => ['placeholder' => 'Saisissez l\'adresse du camping'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse pour le camping']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'L\'adresse doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'L\'adresse ne doit pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('imagecamp', TextType::class, [
                'label' => 'Image du camping',
                'attr' => ['placeholder' => 'URL de l\'image du camping'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez fournir une URL pour l\'image du camping']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Camping::class,
        ]);
    }
}
