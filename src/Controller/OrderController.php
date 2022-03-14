<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class OrderController extends AbstractController
{
    /**
     * @param UserInterface|User $user
     */
    public function create(#[CurrentUser] UserInterface $user, OrderRepository $repository, Request $request): JsonResponse
    {
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

    /**
     * @param UserInterface|User $user
     */
    public function list(#[CurrentUser] UserInterface $user, OrderRepository $repository): JsonResponse
    {
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
