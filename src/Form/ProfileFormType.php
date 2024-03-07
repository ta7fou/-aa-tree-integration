<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email address.',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address.',
                    ]),
                ],
            ])
            ->add('Nom', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z ]+$/',
                        'message' => 'Name can only contain letters and spaces.',
                    ]),
                ],
            ])
            ->add('Prenom', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z ]+$/',
                        'message' => 'Name can only contain letters and spaces.',
                    ]),
                ],
            ])
            ->add('address')
            ->add('dateNaissance', DateType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your date of birth.',
                    ]),
                    new LessThan(['value' => new \DateTimeImmutable('+18 years'), 'message' => 'You must be at least 18 years old to register.']),
                ],
                'format' => 'yyyy-MM-dd', // Adjust format as needed
                'widget' => 'single_text', // Adjust widget if needed
            ])
            ->add('Password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Callback([$this, 'validatePasswordMatch']),
                ],
            ])
            ->add('valider',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }


    public function validatePasswordMatch($value, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot();
        $password = $form->get('Password')->getData();

        if ($password !== $value) {
            $context->buildViolation('The passwords do not match.')
                ->atPath('confirmPassword')
                ->addViolation();
        }
    }

}