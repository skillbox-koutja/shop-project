<?php

namespace App\ReadModel\Shop\Product\Sorter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SorterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('field', Type\ChoiceType::class,
                [
                    'choices' => [
                        'По цене' => Sorter::FIELD_PRICE,
                        'По названию' => Sorter::FIELD_TITLE,
                    ],
                    'required' => false,
                    'placeholder' => 'Сортировка',
                ]
            )
            ->add('order', Type\ChoiceType::class,
                [
                    'choices' => [
                        'По возрастанию' => Sorter::SORT_ASC,
                        'По убыванию' => Sorter::SORT_DESC,
                    ],
                    'required' => false,
                    'placeholder' => 'Порядок',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
