<?php

declare(strict_types=1);

namespace App\Model\Shop\UseCase\Product\Edit;

use App\ReadModel\Shop\Category\CategoryFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class Form extends AbstractType
{
    private CategoryFetcher $categories;

    public function __construct(CategoryFetcher $categories)
    {
        $this->categories = $categories;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', Type\TextType::class)
            ->add('price', Type\IntegerType::class)
            ->add('sale', Type\CheckboxType::class, ['required' => false])
            ->add('new', Type\CheckboxType::class, ['required' => false])
            ->add('categories', Type\ChoiceType::class, [
                'multiple' => true,
                'choices' => array_flip($this->categories->allList()),
            ])
            ->add('photo', Type\FileType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ]
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }

}
