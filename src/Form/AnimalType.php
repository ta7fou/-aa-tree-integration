<?php

namespace App\Form;

use App\Entity\Animals;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Animal Name',
                'attr' => ['placeholder' => 'Enter animal name', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Name cannot be blank.']),
                    new Type(['type' => 'string', 'message' => 'Name must be a string.']),
                    new Regex(['pattern' => '/\d/', 'match' => false, 'message' => 'Name cannot contain numbers.']),
                    new Length(['max' => 255, 'maxMessage' => 'Name cannot be longer than {{ limit }} characters.']),
                ],
            ])
            ->add('espece', TextType::class, [
                'label' => 'Species',
                'attr' => ['placeholder' => 'Enter animal species', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Species cannot be blank.']),
                    new Type(['type' => 'string', 'message' => 'Species must be a string.']),
                    new Regex(['pattern' => '/\d/', 'match' => false, 'message' => 'Species cannot contain numbers.']),
                    new Length(['max' => 255, 'maxMessage' => 'Species cannot be longer than {{ limit }} characters.']),
                ],
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Age',
                'attr' => ['placeholder' => 'Enter animal age', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Age cannot be blank.']),
                    new Type(['type' => 'integer', 'message' => 'Age must be an integer.']),
                ],
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sex',
                'choices' => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                    'Other' => 'Other'
                ],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Sex cannot be blank.']),
                    new Choice([
                        'choices' => ['Male', 'Female', 'Other'],
                        'message' => 'Invalid sex value.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Enter animal description', 'class' => 'form-control'],
                'constraints' => [
                    new Regex(['pattern' => '/\d/', 'match' => false, 'message' => 'Description cannot contain numbers.']),
                    new Length(['max' => 1000, 'maxMessage' => 'Description cannot be longer than {{ limit }} characters.']),
                ],
            ])
            ->add('image', TextType::class, [
                'label' => 'Animal Picture',
                'attr' => ['placeholder' => 'Enter animal picture', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Image cannot be blank.']),
                    new Type(['type' => 'string', 'message' => 'Image must be a string.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animals::class,
        ]);
    }
}
