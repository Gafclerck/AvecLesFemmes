<?php

namespace App\Form;

use App\DTO\GroupSearchDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', SearchType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un groupement par nom...',
                    'autocomplete' => 'off',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GroupSearchDTO::class,
            'method' => 'POST',
            'csrf_protection' => false,
        ]);
    }
}
