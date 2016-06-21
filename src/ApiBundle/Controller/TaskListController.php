<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ApiBundle\Entity\TaskList;
use ApiBundle\Entity\Priority;
use ApiBundle\Form\TaskListType;

class TaskListController extends FOSRestController
{

    public function getTasklistAction($id)
    {
        $taskList = $this
                    ->getDoctrine()
                    ->getRepository('ApiBundle:TaskList')
                    ->find($id);

        if(!$taskList){
            return new View(
                "page not found",
                Response::HTTP_NOT_FOUND);
            }
        
        if($this->checkAccess($taskList)){
            return $taskList;
        } 
        return "permission denied";
    }

    public function getTasklistsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $priorities = $user->getPriorities();
        $taskLists = array();
        foreach ($priorities as $priority) {
            $taskLists[] = $priority->getTaskList();            
        }

        return $taskLists;
    }

    public function postTasklistsAction(Request $request)
    {
        $taskList = new TaskList();
        $errors = $this->treatAndValidateRequest($taskList, $request);
        if (count($errors) > 0) {
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        $this->persistAndFlush($taskList);
        $this->addPriority($taskList);

        return new View($taskList, Response::HTTP_CREATED);
    }

    public function putTasklistAction(TaskList $taskList, Request $request)
    {
        $errors = $this->treatAndValidateRequest($taskList, $request);
        if (count($errors) > 0) {
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if($this->checkAccess($taskList, Priority::CREATOR_PRIORITY)){
            $this->persistAndFlush($taskList); 
            return $taskList;  
        }
        return "permission denied";        
    }

    public function deleteTasklistAction($id)
    {
        //while without delete cascade
        $taskList = $this
            ->getDoctrine()
            ->getRepository('ApiBundle:TaskList')
            ->find($id);
        if(!$taskList){
            return new View(
                "page not found",
                Response::HTTP_NOT_FOUND);
            }
        if($this->checkAccess($taskList, Priority::CREATOR_PRIORITY)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($taskList);
            $em->flush();
            return;
        }
        return "permission denied";
    }

    private function treatAndValidateRequest(TaskList $taskList, Request $request)
    {
        $form = $this->createForm(
            new TaskListType(),
            $taskList,
            array(
                'method' => $request->getMethod()
            )
        );
        $form->handleRequest($request);

        $errors = $this->get('validator')->validate($taskList);
        return $errors;
    }

    private function persistAndFlush(TaskList $taskList)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($taskList);
        $manager->flush();
    }

    private function checkAccess(TaskList $taskList, $priority = null)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if($priority == null){
            $query = array('user' => $user, 'task_list' => $taskList);
        } else {
            $query = array('user' => $user, 'priority' => $priority, 'task_list' => $taskList);
        }

        return $result = $this
            ->getDoctrine()
            ->getRepository('ApiBundle:Priority')
            ->findBy($query);
    }

    private function addPriority(TaskList $taskList)
    {
        $priority = new Priority();
        $priority->setUser($this->get('security.context')->getToken()->getUser());
        $priority->setTaskList($taskList);
        $priority->setPriority(1);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($priority);
        $manager->flush();
        return;
    }

}