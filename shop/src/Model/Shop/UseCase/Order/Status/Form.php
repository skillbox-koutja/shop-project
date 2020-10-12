<?php

namespace App\Model\Shop\UseCase\Order\Status;

use App\Model\Shop\Entity\Delivery;
use App\Model\Shop\Entity\Order\Status;
use App\ReadModel\Shop\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('status', Type\ChoiceType::class,
                [
                    'choices' => [
                        'Выполнено' => Status::DONE,
                        'Не выполнено' => Status::UNDONE,
                    ],
                    'attr' => ['onchange' => 'this.form.submit()']
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
            'csrf_protection' => false
        ));
    }
}
