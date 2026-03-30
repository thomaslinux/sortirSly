<?php

namespace App\Form;

use App\Entity\Campus;
use App\Form\Model\SortieSearch;
use App\Repository\CampusRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie',
                'attr' => ['placeholder' => 'Rechercher...'],
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Tous les campus',
                'query_builder' => function (CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c')->addOrderBy('c.nom');
                }
            ])
            ->add('dateHeureDebut', DateType::class, [
                'label' => 'Entre le'
            ])
            ->add('dateHeureFin', DateType::class, [
                'label' => 'Et le'
            ])
            ->add('organisateur', CheckboxType::class)
            ->add('inscrit', CheckboxType::class)
            ->add('pasInscrit', CheckboxType::class)
            ->add('sortiesPassees', CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SortieSearch::class,
            'required' => false
        ]);
    }
}
