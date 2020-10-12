<?php

declare(strict_types=1);

namespace App\Controller\Shop;

use App\Controller\ErrorHandler;
use App\Model\Shop\UseCase\Product\Create;
use App\Model\Shop\UseCase\Product\Edit;
use App\Model\Shop\UseCase\Product\Remove;
use App\Model\Shop\Entity;
use App\Model\Shop\UseCase\Product\PhotoInfo;
use App\ReadModel\Shop\Product;
use App\ReadModel\Shop\Product\ProductFetcher;
use App\Security\Voter\Shop\ProductAccess;
use App\Service\Uploader\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractController
{
    private const PER_PAGE = 10;
    private ErrorHandler $errors;

    public function __construct(
        ErrorHandler $errors
    )
    {
        $this->errors = $errors;
    }

    public function index(
        Request $request,
        ProductFetcher $productFetcher
    ): Response
    {
        $this->denyAccessUnlessGranted(ProductAccess::VIEW);

        $filter = new Product\Filter\Filter();
        $sorter = Product\Sorter\Sorter::default();

        $pagination = $productFetcher->all(
            $filter,
            $sorter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render(
            'app/product/products.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    public function create(
        Request $request,
        FileUploader $uploader,
        Create\Handler $handler
    ): Response
    {
        $this->denyAccessUnlessGranted(ProductAccess::MANAGE);

        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photo')->getData();
            $uploaded = $uploader->upload($file);
            $photo = new PhotoInfo(
                $uploaded->getPath(),
                $uploaded->getName(),
                $uploaded->getSize()
            );
            $command->photo = $photo;
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Товар успешно добавлен');
                return $this->redirectToRoute('admin_shop_products');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render(
            'app/product/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
    public function edit(
        Entity\Product\Product $product,
        Request $request,
        FileUploader $uploader,
        Edit\Handler $handler
    ): Response
    {
        $this->denyAccessUnlessGranted(ProductAccess::MANAGE);

        $command = Edit\Command::fromProduct($product);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $file = $form->get('photo')->getData();
                if ($file) {
                    $uploaded = $uploader->upload($file);
                    $photo = new PhotoInfo(
                        $uploaded->getPath(),
                        $uploaded->getName(),
                        $uploaded->getSize()
                    );
                    $command->photo = $photo;
                }
                $handler->handle($command);
                $this->addFlash('success', 'Изменения успешно применены');
                return $this->redirectToRoute(
                    'admin_shop_products_edit',
                    [
                        'id' => $product->getId(),
                    ]
                );
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        $info = $product->getPhoto()->getInfo();

        return $this->render(
            'app/product/edit.html.twig',
            [
                'photo' => [
                    'path' => "{$info->getPath()}/{$info->getTitle()}",
                    'alt' => $product->getTitle(),
                ],
                'form' => $form->createView(),
            ]
        );
    }
    public function remove(
        Entity\Product\Product $product,
        Request $request,
        Remove\Handler $handler
    ): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_shop_products');
        }
        $this->denyAccessUnlessGranted(ProductAccess::DELETE);

        $command = Remove\Command::fromProduct($product);
        try {
            $handler->handle($command);
            $this->addFlash('success', 'Товар удален');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_shop_products');
    }

    public function edit_product(): Response
    {
        return $this->render(
            'app/product/edit_product.html.twig'
        );
    }
}
