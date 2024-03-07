<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Campagne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un lieu.',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le lieu doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le lieu ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Event' => 'Event',
                    'Don' => 'Don',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner un type.',
                    ]),
                ],
            ])
            ->add('nbparticipant', IntegerType::class, [
                'label' => 'Nombre de participants',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un nombre de participants.',
                    ]),
                    new Assert\Type([
                        'type' => 'integer',
                        'message' => 'Le nombre de participants doit être un entier.',
                    ]),
                    new Assert\GreaterThan([
                        'value' => 0,
                        'message' => 'Le nombre de participants doit être supérieur à zéro.',
                    ]),
                ],
            ])
            ->add('objectif', IntegerType::class, [
                'label' => 'Objectif',
                'required' => false, // Rendre le champ facultatif
                'constraints' => [
                    new Assert\Type([
                        'type' => 'integer',
                        'message' => 'L\'objectif doit être un entier.',
                    ]),
                ],
            ])
            ->add('campagne', EntityType::class, [
                'class' => Campagne::class,
                'choice_label' => 'nomcampagne',
                'label' => 'Campagne',
                'placeholder' => 'Sélectionnez une campagne',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une campagne.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer l\'événement']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
