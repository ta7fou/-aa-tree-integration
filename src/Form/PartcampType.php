<?php

namespace App\Form;
use App\Entity\PartCamp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class PartcampType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                
        ->add('datenaissance', DateType::class, [
            'label' => 'Date of Birth',
            'constraints' => [
                new NotBlank(['message' => 'Please enter your date of birth.']),
                new LessThan([
                    'value' => new \DateTimeImmutable('-18 years'),
                    'message' => 'You must be at least 18 years old to register.',
                ]),
            ],
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'attr' => ['placeholder' => 'Select your date of birth'],
        ])
        
            ->add('nomuser' , TextType::class, [
                'label' => 'Nom du user',
                'attr' => ['placeholder' => 'Saisissez votre nom '],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre nom']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            
            ->add('prenomuser', TextType::class, [
                'label' => 'Prenom du user',
                'attr' => ['placeholder' => 'Saisissez votre prénom '],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre prenom']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le prenom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le prenom ne doit pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('user_id', HiddenType::class )

            ->add('camping_id',HiddenType::class)
            ->add('emailuser' , null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email address.',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address.',
                    ]),
                ],
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Submit',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PartCamp::class,
        ]);
    }
}
