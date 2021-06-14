<?php

namespace App\Controller;

use App\Entity\User;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Config\TwigConfig;
use Symfony\Flex\Configurator\ContainerConfigurator;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;


class AuthController extends AbstractController{
    public function register(): Response{

        $request = Request::createFromGlobals();
        $message = null;
        if($request->isMethod('post'))
        {
            $user = new User();
            $user->setUsername($request->request->get('username'));
            $user->setEmail($request->request->get('email'));
            $user->setPermission(0);
            $user->setPassword($this->hashPassword($request->request->get('password')));

            try{
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $message = 'User '.$user->getUsername().' successfully registered.';
            }
            catch (\Exception $e){
                $message = 'User coudn\'t be registered. Exception cought: '.$e->getMessage();
            }
        }
        (new ViewController)->setGlobals($this->getDoctrine(),'View/register.html.twig',['message'=>$message]);
        return new Response();
    }

    public function login(): Response
    {
        $request = Request::createFromGlobals();
        $message = null;
        if($request->isMethod('post')){
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=>$request->request->get('email')]);
            if(!$user){
                $message = "User not found.";
            }else if ($this->verifyPassword($user->getPassword(),$request->request->get('password'))) {
                $message = "Logged in as " . $user->getUsername();
                $session = new Session();
                $session->set('userId', $user->getId());
//                    $loader = new FilesystemLoader('../templates');
//                    $twig = new Environment($loader);
//                    $twig->addGlobal('user',$user);
            }
            else
                $message = "Password is incorrect " . $user->getUsername();
        }

        (new ViewController)->setGlobals($this->getDoctrine(),'View/login.html.twig',['message'=>$message]);
        return new Response();
    }

    public function logout(): RedirectResponse
    {
        $request = new Request();
        $request->getSession()->clear();
        $_SESSION = [];
        if(ini_get('session.use_cookies')){
            $params = session_get_cookie_params();
        }
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
        session_destroy();

        $this->addFlash('success',"You have been logged out");
        return $this->redirectToRoute('index');
    }

    public function hashPassword(string $password): string
    {
        $factory = new PasswordHasherFactory([
           'common'=>['algorithm'=>'bcrypt'],
           'memory-hard'=>['algorithm'=>'sodium'], 
        ]);
        $passwordHasher = $factory->getPasswordHasher('common');
        return $passwordHasher->hash($password);
    }
    public function verifyPassword(string $hash, string $password)
    {
        $factory = new PasswordHasherFactory([
            'common'=>['algorithm'=>'bcrypt'],
            'memory-hard'=>['algorithm'=>'sodium'],
        ]);
        $passwordHasher = $factory->getPasswordHasher('common');
        return $passwordHasher->verify($hash,$password);
    }
}