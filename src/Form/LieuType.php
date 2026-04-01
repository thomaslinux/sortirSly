<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder

            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label' => 'Ville :',
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom du lieu :',
            ])->add('rue', TextType::class, [
                'label' => 'Rue :'
            ])
            ->add('latitude', NumberType::class, [
                'scale' => 5, //  nombre de décimales
                'attr' => ['step' => 'any'], // permet la saisie de plus de décimales
                'label' => 'Latitude :'
            ])
            ->add('longitude', NumberType::class, [
                'scale' => 5,
                'attr' => ['step' => 'any'],
                'label' => 'longitude :'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
            'require' => true

        ]);
    }
}
