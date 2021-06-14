<?php
namespace App\Controller;

use App\Entity\TodoList;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\String\b;

class UserController extends AbstractController{

    public function user(string $name): Response{
        $lists = "";
        $message = '';
        $error = false;
        $request = Request::createFromGlobals();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username'=>$name]);
        if($user) {
            $lists = $this->getDoctrine()->getRepository(TodoList::class)->findBy(['owner' => $user->getId()]);
            if($request->isMethod('post'))
            {
                switch ($user->getPermission()){
                    case 0:
                        $user->setPermission(1);
                        break;
                    case 1:
                        $user->setPermission(0);
                        break;
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $message = "Permission successfully toggled";
            }
        }
        else
           $error = true;
        (new ViewController)->setGlobals($this->getDoctrine(),'View/User/user.html.twig',['lists'=>$lists,'usr'=>$user, 'message'=>$message,'error'=>$error]);
        return new Response();
    }

    public function view(string $name): Response{
        $request = Request::createFromGlobals();
        $message = "";
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username'=>$name]);
        if($user)
        {
            $error = false;
            if($request->isMethod('post')){
                if((new AuthController)->verifyPassword($user->getPassword(),$request->request->get('inputPassword'))){
                    if($request->request->get('inputPwd') !== "")
                        $user->setPassword((new AuthController)->hashPassword($request->request->get('inputPwd')));
                    $user->setUsername($request->request->get('inputUsername'));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    return $this->redirectToRoute('userpanel',['name'=>$user->getUsername()]);
                }else{
                    $message = "Incorect password";
                }
            }
        }
        else
            $error = true;

        (new ViewController)->setGlobals($this->getDoctrine(),'View/User/userpanel.html.twig',['usr'=>$user,'error'=>$error,'message'=>$message]);
        return new Response();
    }
}