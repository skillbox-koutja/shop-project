<?php

namespace App\Twig\Widget\Shop\Order;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AddressWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'order_address',
                [$this, 'widget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function widget(Environment $twig, $order): string
    {
        return $twig->render('app/widget/order/address.html.twig', [
            'order' => $order
        ]);
    }
}
