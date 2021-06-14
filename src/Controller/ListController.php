<?php
namespace App\Controller;

use App\Entity\ListEntry;
use App\Entity\TodoList;
use App\Repository\TodoListRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ListController extends AbstractController{
    public function addList():Response{

        $request = Request::createFromGlobals();
        $session = new Session();
        $message = '';
        if($request->isMethod('post'))
        {
            $list = new TodoList();
            $list->setOwner($session->get('userId'));
            $list->setTitle($request->request->get('titleInput'));
            $list->setShareable($request->request->get('shareableCheckbox') == 'on');

            try{
                $em = $this->getDoctrine()->getManager();
                $em->persist($list);
                $em->flush();
                $message = "List was successfuly created";
            }catch (Exception $e){
                $message = "Something went wrong. " . $e->getMessage();
            }
        }


        (new ViewController)->setGlobals($this->getDoctrine(),'View/List/add.html.twig',['message'=>$message]);
        return new Response();
    }

    public function allLists() : Response{
        $message = '';

        $lists = $this->getDoctrine()->getRepository(TodoList::class)->findAll();
        (new ViewController)->setGlobals($this->getDoctrine(),'View/List/lists.html.twig',['message'=>$message, 'lists'=>$lists]);
        return new Response();
    }

    public function addEntry(int $id) : Response{
        $message = '';

        $request = Request::createFromGlobals();
        if($request->isMethod('post'))
        {
            $entry = new ListEntry();
            $entry->setTitle($request->request->get('inputTitle'));
            $entry->setDescription($request->request->get('inputDesc'));
            $entry->setStatus(false);
            $entry->setListId($id);
            try{
                $em = $this->getDoctrine()->getManager();
                $em->persist($entry);
                $em->flush();
                $message = 'Entry was successfully added';
            }catch (Exception $e){
                $message = 'Something went wrong ' . $e->getMessage();
            }
        }
        $list = $this->getDoctrine()->getRepository(TodoList::class)->find($id);


        (new ViewController)->setGlobals($this->getDoctrine(),'View/List/addEntry.html.twig',['message'=>$message,'list'=>$list]);
        return new Response();
    }

    public function detailedList(int $id) : Response{
        $message = '';
        $list = $this->getDoctrine()->getRepository(TodoList::class)->find($id);

        $request = Request::createFromGlobals();
        if($request->isMethod('post'))
        {
            if($request->request->get('toggleShare') !== null) {
                $em = $this->getDoctrine()->getManager();
                $list->setShareable(!$list->getShareable());
                $em->persist($list);
                $em->flush();
                $accessibility = $list->getShareable() == 1 ? 'shared' : 'private';
                $message = "Accessibility was changed to " . $accessibility;
            }else if($request->request->get('complete') !== null){
                $entry = $this->getDoctrine()->getRepository(ListEntry::class)->find($request->request->get('entryId'));
                $entry->setStatus(!$entry->getStatus());
                $em = $this->getDoctrine()->getManager();
                $em->persist($entry);
                $em->flush();
                $cat = $entry->getStatus() ? 'completed' : 'planned';
                $message = "Entry was moved to " . $cat;
            }else if($request->request->get('delete') !== null) {
                $entry = $this->getDoctrine()->getRepository(ListEntry::class)->find($request->request->get('entryId'));
                $em = $this->getDoctrine()->getManager();
                $em->remove($entry);

                $em->flush();
                $message = "Entry was successfully deleted";
            }
        }
        $entries = $this->getDoctrine()->getRepository(ListEntry::class)->findBy(['listId'=>$id]);


        (new ViewController)->setGlobals($this->getDoctrine(),'View/List/list.html.twig',['message'=>$message, 'list' => $list, 'entries'=>$entries]);
        return new Response();
    }
}