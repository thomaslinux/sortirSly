<?php

namespace App\Form;

use App\Dto\UserImport;
use App\Entity\Campus;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('csvFile', FileType::class, [
                'label' => 'Fichier CSV (pseudo;nom;prenom;mail;telephones)',
                'required' => true,
                'mapped' => true
            ])
            ->add('campus', EntityType::class, ['class' => Campus::class, 'choice_label' => 'nom', 'required' => true,'mapped'=> true,
                'query_builder' => function (CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c')->addOrderBy('c.nom', 'ASC');
                }])
            ->add('plainPassword', RepeatedType::class,
                ['type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent correspondre.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options' => ['label' => 'Mot de passe : '],
                    'second_options' => ['label' => 'Confirmation Mot de passe : '],
                    'mapped' => true,
                    'constraints' => [new Assert\NotBlank(), new Assert\Regex(['pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.',]),
                        new Assert\Length([
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères.'])
                ]]);

}

public
function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => UserImport::class
    ]);
}
}
