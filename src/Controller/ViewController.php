<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ViewController extends AbstractController{
    public function index(): Response
    {
        return $this->render('View/index.html.twig');
    }
}