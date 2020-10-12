<?php

namespace App\Twig\Widget\Shop\Order;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'order_status',
                [$this, 'widget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function widget(Environment $twig, string $status): string
    {
        return $twig->render('app/widget/order/status.html.twig', [
            'status' => $status,
        ]);
    }
}
