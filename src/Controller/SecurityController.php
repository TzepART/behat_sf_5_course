<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        $tools = $this->get('security.authentication_utils');

        return $this->render('main/login.html.twig', [
            'error' => $tools->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/admin/login_check", name="admin_login_check")
     */
    public function loginCheck()
    {

    }

    /**
     * @Route("/admin/logout", name="admin_logout")
     */
    public function logout()
    {

    }
}
