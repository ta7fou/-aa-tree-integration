<?php

namespace App\Form;
use App\Entity\Campagne;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class CampagneType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datedeb', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Sélectionnez la date de début'],
                'constraints' => [
                    new NotBlank(['message' => '<b>Veuillez entrer une date de début</b>']),
                    new GreaterThan([
                        'value' => new \DateTime(), // La date de début doit être postérieure à la date actuelle
                        'message' => 'La date de début doit être postérieure à la date actuelle'
                    ])
                ]
            ])
            ->add('datefin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Sélectionnez la date de fin'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une date de fin']),
                    new GreaterThan([
                        'value' => new \DateTime(), // La date de fin doit être postérieure à la date actuelle
                        'message' => '<b>La date de fin doit être postérieure à la date actuelle</b>'
                    ])
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPG, PNG)',
                'required' => false,
                'mapped' => false, // Ne pas mapper ce champ à l'entité Campagne

            ])
            ->add('createur', TextType::class, [
                'label' => 'Créateur',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le nom du créateur'],
                'constraints' => [
                    new NotBlank(['message' => '<b>Veuillez entrer le nom du créateur</b>'])
                ],
            ])
            ->add('nomcampagne', TextType::class, [
                'label' => 'Nom de la campagne',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le nom de la campagne'],
                'constraints' => [
                    new NotBlank(['message' => '<b>Veuillez entrer le nom de la campagne</b>']),
                    new Callback([$this, 'validateUniqueName'])
                ]
            ])
            ->add('descri', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Entrez une description de la campagne'],
                'constraints' => [
                    new NotBlank(['message' => '<b>Veuillez entrer une description de la campagne</b>'])
                ]
            ]);
    }

    public function validateUniqueName($value, ExecutionContextInterface $context)
    {
        // Récupérer l'entité actuelle (la campagne si elle existe déjà)
        $campagne = $context->getRoot()->getData();

        // Vérifier si le nom de la campagne est unique dans la base de données
        $existingCampagne = $this->entityManager->getRepository(Campagne::class)->findOneBy(['nomcampagne' => $value]);

        if ($existingCampagne && $existingCampagne !== $campagne) {
            $context->buildViolation('Ce nom de campagne est déjà utilisé.')
                ->atPath('nomcampagne')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campagne::class,
        ]);
    }
    
}
