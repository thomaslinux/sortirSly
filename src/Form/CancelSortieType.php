<?php

namespace App\Form;

use App\Form\Model\CancelSortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CancelSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('descriptionCancel', TextareaType::class, [
                'label' => 'Motif de l\'annulation :',
                'attr' => [
                    'class' => 'reason-textarea'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CancelSortie::class,
            'required' => false,
            'user' => null
        ]);
    }
}
