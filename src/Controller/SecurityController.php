<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    public function login(): Response
    {
        $user = $this->getUser();

        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $token = 'token';

        return $this->json([
              'user'  => $user->getUserIdentifier(),
              'token' => $token,
        ]);
    }


    public function logout(): Response
    {
        return $this->json([
            'result' => 'OK'
        ]);
    }
}
