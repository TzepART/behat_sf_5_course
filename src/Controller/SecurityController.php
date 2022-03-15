<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SecurityController extends AbstractController
{
    /**
     * @param UserInterface|User|null $user
     */
    public function login(#[CurrentUser] ?UserInterface $user, UserRepository $userRepository): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = uniqid();
        $userRepository->updateApiToken($user->getUserIdentifier(), $token);

        return $this->json([
              'user_id'  => $user->getUserIdentifier(),
              'token' => $token,
        ]);
    }

    /**
     * @param UserInterface|User|null $user
     */
    public function logout(#[CurrentUser] ?UserInterface $user, UserRepository $userRepository): Response
    {
        if($user instanceof User){
            $userRepository->updateApiToken($user->getUserIdentifier(), null);
        }

        return $this->json([
            'result' => 'OK'
        ]);
    }
}
