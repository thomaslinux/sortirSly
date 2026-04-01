<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use App\Service\SortieService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'widget' => 'single_text',
                'attr' => [
                    'id' => 'dateHeureDebut',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                ],
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription :',
                'attr' => [
                    'id' => 'dateLimiteInscription',
                    'min' => (new \DateTime())->format('Y-m-d'),
                ],
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes):',
                'attr' => [
                    'value' => '5',
                    'placeholder' => '5',
                    'min' => '5'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description et infos :',
                'attr' => [
                    'rows' => 5,
                    'cols' => 40
                ]
            ])
            ->add('nbPlaces', IntegerType::class, [
                'label' => 'Nombre de places (> 2) : ',
                'attr' => [
                    'value' => '2',
                    'placeholder' => '2',
                    'min' => '2'
                ]
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'query_builder' => function (CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c')->addOrderBy('c.nom');
                }
            ])
            ->add('villes', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '- Choisir une ville -',
                'query_builder' => function (VilleRepository $villeRepository) {
                    return $villeRepository->createQueryBuilder('v')->addOrderBy('v.nom');
                }
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'label' => 'lieu de sortie',
                'mapped' => false,
                'choice_label' => 'nom',
                'choice_value' => 'id',
                'placeholder' => '- Choisir un lieu -',
                'required' => false
            ])
            ->add('lieu2', LieuType::class, [
                'label' => 'Ajoute un lieu de sortie',
                'mapped' => false,
                'required' => false,
            ])
            ->add('publier', SubmitType::class, ['label' => 'Publier']);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'required' => false,
            'user' => null
        ]);
    }
}
