<?php

namespace App\Twig\Widget\Shop\Order;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ToggleStatusWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'order_toggle_status',
                [$this, 'widget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function widget(Environment $twig, $id, string $status): string
    {
        return $twig->render('app/widget/order/toggle_status.html.twig', [
            'id' => $id,
            'status' => $status,
        ]);
    }
}
