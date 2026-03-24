<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => 'Username :'])
            ->add('prenom', TextType::class, ['label' => 'Prenom :'])
            ->add('nom', TextType::class, ['label' => 'Nom :'])
            ->add('tel', TelType::class,  ['label' => 'Telephone :'])
            ->add('email', EmailType::class, ['label' => 'Email :'])
            ->add('campus', EntityType::class, ['class' => Campus::class, 'choice_label' => 'nom', 'required' => true, 'query_builder' => function (CampusRepository $campusRepository) {
                return $campusRepository->createQueryBuilder('c')->addOrderBy('c.nom', 'ASC');
            }])
            ->add('plainPassword', RepeatedType::class, ['type' => PasswordType::class, 'invalid_message' => 'Les mots de passe doivent correspondre.', 'options' => ['attr' => ['class' => 'password-field']], 'required' => false, 'first_options' => ['label' => 'Mot de passe : '], 'second_options' => ['label' => 'Confirmation Mot de passe : '], 'mapped' => false])
            ->add('photo', FileType::class,  ['mapped' => false, 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
