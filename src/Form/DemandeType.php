<?php

namespace App\Form;

use App\Entity\Demande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('animalId', TextType::class, [
                'disabled' => true,
                'label' => 'Animal ID',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Subject',
                'attr' => ['placeholder' => 'Enter the subject', 'class' => 'form-control'],
                'constraints' => [
                    new Length(['max' => 20, 'maxMessage' => 'Subject cannot be longer than {{ limit }} characters.']),
                    new Regex([
                        'pattern' => '/^\D+$/',
                        'message' => 'Subject should not contain numbers.',
                    ]),
                ],
            ])
            ->add('details', TextareaType::class, [
                'label' => 'Details',
                'attr' => ['placeholder' => 'Enter the details', 'class' => 'form-control'],
                'constraints' => [
                    new Length(['min' => 20, 'minMessage' => 'Details must be at least {{ limit }} characters long.']),
                ],
            ]);

        // Only add the email field if the user is authenticated
        if ($options['user']) {
            $builder->add('email', TextType::class, [
                'label' => 'Email',
                'disabled' => true,
                'data' => $options['user']->getEmail(),
                'attr' => ['class' => 'form-control'],
            ]);
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
            'user' => null, // Default to null to handle the case when the user is not authenticated
            'is_edit' => false, // Default value for the is_edit option
        ]);
    }
}
