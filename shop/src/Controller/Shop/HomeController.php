<?php

declare(strict_types=1);

namespace App\Controller\Shop;

use App\ReadModel\Shop\Product;
use App\ReadModel\Shop\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    private const PER_PAGE = 9;

    private Product\ProductFetcher $productFetcher;
    private Category\CategoryFetcher $categoryFetcher;

    public function __construct(
        Product\ProductFetcher $productFetcher,
        Category\CategoryFetcher $categoryFetcher
    )
    {
        $this->productFetcher = $productFetcher;
        $this->categoryFetcher = $categoryFetcher;
    }

    public function delivery()
    {
        return $this->render('app/delivery.html.twig');
    }

    public function index(
        Request $request,
        ?string $preset = null
    ): Response
    {
        $parameters = [
            'new' => filter_var($request->get('new', 0), FILTER_VALIDATE_BOOLEAN),
            'sale' => filter_var($request->get('sale', 0), FILTER_VALIDATE_BOOLEAN),
            'minPrice' => filter_var($request->get('minPrice', -1), FILTER_VALIDATE_INT),
            'maxPrice' => filter_var($request->get('maxPrice', -1), FILTER_VALIDATE_INT),
            'category' => $request->get('category', 'all'),
            'sortingField' => $request->get('sortingField'),
            'sortingOrder' => $request->get('sortingOrder'),
        ];

        if ($preset) {
            $parameters[$preset] = 1;
        }
        if (empty($parameters['sortingField']) || empty($parameters['sortingOrder'])) {
            $sorter = Product\Sorter\Sorter::default();
            $sorterForm = $this->createForm(Product\Sorter\SorterForm::class);
        } else {
            $sorter = new Product\Sorter\Sorter(
                $parameters['sortingField'],
                $parameters['sortingOrder']
            );
            $sorterForm = $this->createForm(Product\Sorter\SorterForm::class, $sorter);
        }
        $parameters['sorterForm'] = $sorterForm->createView();

        $filter = new Product\Filter\Filter();

        if (isset($parameters['category'])) {
            $activeCategory = $this->categoryFetcher->findBySlug($parameters['category']);
            if ($activeCategory) {
                $filter->categories[] = $activeCategory->getId();
            }
        } else {
            $parameters['category'] = 'all';
        }
        if ($parameters['new']) {
            $new = $this->categoryFetcher->findBySlug('new');
            if ($new) {
                $filter->categories[] = $new->getId();
            }
        }
        if ($parameters['sale']) {
            $sale = $this->categoryFetcher->findBySlug('sale');
            if ($sale) {
                $filter->categories[] = $sale->getId();
            }
        }

        $minPrice = $this->productFetcher->minPrice($filter->categories);
        $maxPrice = $this->productFetcher->maxPrice($filter->categories);
        if (
            (isset($parameters['minPrice']) && $parameters['minPrice'] >= 0)
            && (isset($parameters['maxPrice']) && $parameters['maxPrice'] >= 0)
        ) {
            $filter->min = $parameters['minPrice'];
            $filter->max = $parameters['maxPrice'];
            $parameters['slider'] = [
                'minPrice' => $filter->min,
                'maxPrice' => $filter->max,
            ];
        } else {
            $parameters['slider'] = [
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
            ];
        }
        $parameters['minPrice'] = $minPrice;
        $parameters['maxPrice'] = $maxPrice;

        $pagination = $this->productFetcher->feed(
            $filter,
            $sorter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        $parameters['pagination'] = $pagination;
        $parameters['total'] = $pagination->getTotalItemCount();
        $parameters['categories'] = $this->fetchCategories();
        $parameters['categories'] = $this->fetchCategories();

        return $this->render(
            'app/home.html.twig',
            $parameters
        );
    }

    private function fetchCategories()
    {
        $categories = [
            ['id' => 'all', 'title' => 'Все']
        ];
        foreach ($this->categoryFetcher->allList() as $id => $title) {
            $categories[] = ['id' => $id, 'title' => $title];
        }

        return $categories;
    }
}
