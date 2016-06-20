<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ApiBundle\Entity\TaskList;
use ApiBundle\Form\TaskListType;

class TaskListController extends FOSRestController
{

    public function getTasklistAction($id)
    {
        //+ tasks[]
        $taskList = $this
            ->getDoctrine()
            ->getRepository('ApiBundle:TaskList')
            ->find($id);

        if(!$taskList){
            return new View(
                "page not found",
                Response::HTTP_NOT_FOUND);
        }
        return $taskList;
    }

    public function getTasklistsAction()
    {
        //all accessable tasklists must be here
        /*

        return $taskLists;*/
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

        $this->persistAndFlush($taskList);

        return $taskList;
    }

    public function deleteTasklistAction($id)
    {
        $taskList = $this
            ->getDoctrine()
            ->getRepository('ApiBundle:TaskList')
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($taskList);
        $em->flush();

        return;
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

}