<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    public function create(OrderRepository $repository, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $code = $request->get('code') ?? uniqid();
        $order = (new Order())
            ->setCode($code)
            ->setCustomer($user);

        $repository->add($order);

        return $this->json([
            'result' => 'OK',
            'code' => $code
        ]);
    }

    public function list(OrderRepository $repository): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $orders = $repository->findBy(['customer' => $user]);

        return $this->json(
            array_map(function (Order $order){
                return [
                    'id' => $order->getId(),
                    'customer_id' => $order->getCustomer()->getId(),
                    'code' => $order->getCode(),
                ];
            }, (array) $orders)
        );
    }
}
