<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function login(AuthenticationUtils $utils): Response
    {
        return $this->render('main/login.html.twig', [
            'error' => $utils->getLastAuthenticationError()
        ]);
    }

    public function loginCheck()
    {
    }

    public function logout()
    {

    }
}
