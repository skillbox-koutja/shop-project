<?php

namespace App\Twig\Widget\Shop;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryListWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'category_list',
                [$this, 'list'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function list(Environment $twig, string $category, $categories): string
    {
        return $twig->render('app/widget/category_list.html.twig', [
            'category' => $category,
            'categories' => $categories
        ]);
    }
}
