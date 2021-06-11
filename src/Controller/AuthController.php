<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends AbstractController{

    public function login(): Response
    {
        return $this->render('View/login.html.twig');
    }

    public function register(): Response
    {

        $request = Request::createFromGlobals();
        $username = $request->query->get('username');

        return $this->render('View/register.html.twig', ['test'=>$username]);
    }
}