<?php

namespace App\Model\Shop\UseCase\Order\Purchase\Delivery;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', Type\TextType::class)
            ->add('street', Type\TextType::class)
            ->add('house', Type\TextType::class)
            ->add('apartment', Type\TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class,
            'csrf_protection' => false,
        ));
    }
}
