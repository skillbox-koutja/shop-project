<?php

namespace App\Model\Shop\UseCase\Order\Purchase;

use App\Model\Shop\Entity\Delivery;
use App\ReadModel\Shop\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private Payment\MethodFetcher $paymentMethodFetcher;

    public function __construct(
        Payment\MethodFetcher $paymentMethodFetcher
    )
    {
        $this->paymentMethodFetcher = $paymentMethodFetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', Type\TextType::class)
            ->add('firstName', Type\TextType::class)
            ->add('patronymic', Type\TextType::class, [
                'required' => false,
            ])
            ->add('phone', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('deliveryMethodType', Type\ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Самовывоз' => Delivery\Method\Type::PICKUP,
                    'Курьерская доставка' => Delivery\Method\Type::COURIER,
                ],
            ])
            ->add('city', Type\TextType::class, [
                'required' => false,
            ])
            ->add('street', Type\TextType::class, [
                'required' => false,
            ])
            ->add('house', Type\TextType::class, [
                'required' => false,
            ])
            ->add('apartment', Type\TextType::class, [
                'required' => false,
            ])
            ->add('paymentMethodId', Type\ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => array_flip($this->paymentMethodFetcher->allList()),
            ])
            ->add('note', Type\TextareaType::class, [
                'required' => false,
            ])
            ->add('productId', Type\HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if (Delivery\Method\Type::COURIER == $data->deliveryMethodType) {
                    return ['delivery_method_courier'];
                }
                return [];
            }
        ));
    }
}
