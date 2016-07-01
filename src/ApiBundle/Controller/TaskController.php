<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Task;
use ApiBundle\Entity\TaskList;
use ApiBundle\Form\TaskType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Task controller.
 *
 * @Route("/api/task")
 */
class TaskController extends FOSRestController
{

    /**
     * Task get.
     * @Route("/{id}", name="task_get")
     * @Method("GET")
     * @ParamConverter("task", class="ApiBundle:Task", options = {"mapping" : {"id" : "id"}})
     */
    public function getTaskAction(Task $task)
    {
        return View::create(
            $task,
            Response::HTTP_OK
        );
    }

    /*
     * @Route("/addTask/{taskListName}", name="list_add")
     * @Method("POST")
     *//*
    public function addTaskEnyList($taskListName){
        $TaskList = new TaskList();
        $TaskList->setName($taskListName);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($TaskList);
        $manager->flush();
    }*/




    /**
     * Task create.
     *
     * @Route("/tasklist/{id}", name="task_create")
     * @Method("POST")
     * @ParamConverter("taskList", class="ApiBundle:TaskList", options = {"mapping" : {"id" : "id"}})
     */
    public function createTaskAction(TaskList $taskList, Request $request)
    {
        $task = new Task();
        $errors = $this->treatAndValidateRequest($task, new TaskType(), $request);
        if (count($errors) > 0)
            return View::create(
                $errors,
                Response::HTTP_BAD_REQUEST
            );
        $task->setTaskList($taskList);
        $this->persistAndFlush($task);
        
        return View::create(
            $task,
            Response::HTTP_OK
        );
    }

    /**
     * Task edit.
     *
     * @Route("/{id}", name="task_edit")
     * @Method("PUT")
     *  @ParamConverter("task", class="ApiBundle:Task", options = {"mapping" : {"id" : "id"}})
     */
    public function editTaskAction(Task $task, Request $request)
    {
        $errors = $this->treatAndValidateRequest($task, new TaskType(), $request);
        if (count($errors) > 0)
            return View::create(
                $errors,
                Response::HTTP_BAD_REQUEST
            );
        $this->persistAndFlush($task);

        return View::create(
            $task,
            Response::HTTP_OK
        );
    }


    /**
     * Task delete.
     *
     * @Route("/{id}", name="task_delete")
     * @Method("DELETE")
     * @ParamConverter("task", class="ApiBundle:Task", options = {"mapping" : {"id" : "id"}})
*/
    public function deleteTaskAction(Task $task)
    {
            $this->persistAndFlush($task, false);
    }



    private function treatAndValidateRequest($object, $formType, Request $request)
    {
        $form = $this->createForm(
            $formType,
            $object,
            [
                'method' => $request->getMethod()
            ]
        );

        $form->handleRequest($request);
        $errors = $this->get('validator')->validate($object);

        return $errors;
    }

    private function persistAndFlush($object, $create = true)
    {
        $manager = $this->getDoctrine()->getManager();
        if ($create)
            $manager->persist($object);
        else
            $manager->remove($object);
        $manager->flush();
    }
}
