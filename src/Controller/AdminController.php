<?php
namespace App\Controller;

use App\Entity\TodoList;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController{
    public function view() : Response{
        (new ViewController)->setGlobals($this->getDoctrine(),'View/Admin/panel.html.twig',[]);
        return new Response();
    }

    public function users() : Response{
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        (new ViewController)->setGlobals($this->getDoctrine(),'View/Admin/users.html.twig',['users'=>$users]);
        return new Response();
    }
    public function lists() : Response{
        $lists = $this->getDoctrine()->getRepository(TodoList::class)->findAll();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        (new ViewController)->setGlobals($this->getDoctrine(),'View/Admin/lists.html.twig',['lists'=>$lists,'users'=>$users]);
        return new Response();
    }
}