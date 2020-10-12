<?php

declare(strict_types=1);

namespace App\Controller\Shop;

use App\Controller\ErrorHandler;
use App\Model\Shop\Entity\Order;
use App\Model\Shop\Entity\Product\Product;
use App\Model\Shop\UseCase;
use App\ReadModel\Shop\Order\OrderFetcher;
use App\ReadModel\Shop\Payment;
use App\ReadModel\Shop\Delivery;
use App\Security\Voter\Shop\OrderAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends AbstractController
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
        OrderFetcher $fetcher
    ): Response
    {
        $this->denyAccessUnlessGranted(OrderAccess::VIEW);

        $pagination = $fetcher->feed(
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render(
            'app/order/orders.html.twig',
            [
                'pagination' => $pagination,
                'total' => $pagination->getTotalItemCount(),
            ]
        );
    }

    public function changeStatus(
        Request $request,
        Order\Order $order,
        UseCase\Order\Status\Handler $statusHandler
    ): Response
    {
        $this->denyAccessUnlessGranted(OrderAccess::MANAGE, $order);
        if (Request::METHOD_POST === $request->getMethod()
            && !$this->isCsrfTokenValid('change_status', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_shop_orders');
        }

        $command = UseCase\Order\Status\Command::fromOrder($order);
        try {
            $command->status = (string) $request->request->get('status');
            $statusHandler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_shop_orders');
    }

    public function purchase(
        Request $request,
        Product $product,
        Payment\MethodFetcher $paymentMethodFetcher,
        Delivery\MethodFetcher $deliveryMethodFetcher,
        UseCase\Order\Purchase\Handler $handler
    ): Response
    {
        if (Request::METHOD_POST === $request->getMethod()
            && !$this->isCsrfTokenValid('purchase', $request->request->get('token'))) {
            return $this->redirectToRoute('home');
        }

        $command = UseCase\Order\Purchase\Command::fromProduct(
            $product,
            $deliveryMethodFetcher->getDefaultMethodType(),
            $paymentMethodFetcher->defaultMethodId()
        );
        $form = $this->createForm(UseCase\Order\Purchase\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->render('app/order/success-purchase.html.twig');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render(
            'app/order/purchase.html.twig',
            [
                'order' => $command,
                'form' => $form->createView(),
                'deliveryDate' => [
                    'min' => (new \DateTime())
                        ->add(new \DateInterval('P1D'))
                        ->format('d M'),
                    'max' => (new \DateTime())
                        ->add(new \DateInterval('P3D'))
                        ->format('d M'),
                ]
            ]
        );
    }
}
