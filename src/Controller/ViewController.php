<?php
namespace App\Controller;

use App\Entity\TodoList;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ViewController extends AbstractController{

    public function index() : Response{
        $message = '';
        $request = Request::createFromGlobals();
        if($request->isMethod('post'))
        {
            $session = new Session();
            $user = $this->getDoctrine()->getRepository(User::class)->find($session->get('userId'));
            $user->setPermission(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        (new ViewController)->setGlobals($this->getDoctrine(),'View/index.html.twig',['message'=>$message]);
        return new Response();
    }




    public function setGlobals($doctrine, $template, $args)
    {
        static $twig = null;
        $session = new Session();

        if ($twig === null) {

            $loader = new \Twig\Loader\FilesystemLoader('../templates');
            $twig = new \Twig\Environment($loader);
            if($session->get('userId') != null) {
                $twig->addGlobal('user', $this->getUserById($session->get('userId'), $doctrine));
            } // Saves the value obtained from the method to a twig global variable
        }//return $twig->render($template, $args);

        echo $twig->render($template, $args);
    }

    private function getUserById(int $id, $doctrine): ?object
    {
        return $doctrine->getRepository(User::class)->find($id);
    }
}