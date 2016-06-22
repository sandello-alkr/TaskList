<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\Common\Annotations\AnnotationReader;

use ApiBundle\Entity\TaskList;
use ApiBundle\Entity\Priority;
use ApiBundle\Form\TaskListType;

class TaskListController extends FOSRestController
{

    public function getTasklistAction($id)
    {
        try {
            $taskList = $this
                        ->getDoctrine()
                        ->getRepository('ApiBundle:TaskList')
                        ->find($id);

            if(!$taskList){
                throw new NotFoundHttpException("Page not found.");
                }
            
            if(!$this->checkAccess($taskList)){
                throw new AccessDeniedException();
            } 
            return $this->getSerializer($taskList, array("list_data"));
        } 
        catch (NotFoundHttpException $e){
            return new View(
                    ["error" => $e->getMessage()],
                    Response::HTTP_NOT_FOUND);
        }
        catch (AccessDeniedException $e){
            return new View(
                ["error" => $e->getMessage()],
                Response::HTTP_FORBIDDEN);
        }
        
    }

    public function getTasklistsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $priorities = $user->getPriorities();
        $taskLists = array();
        foreach ($priorities as $priority) {
            $taskLists[] = $priority->getTaskList();            
        }

        return $this->getSerializer($taskLists, array("list_data"));
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

    public function putTasklistAction($id, Request $request)
    {
        try {
            $taskList = $this
                ->getDoctrine()
                ->getRepository('ApiBundle:TaskList')
                ->find($id);
            if(!$taskList){
                throw new NotFoundHttpException();            
                }

            $errors = $this->treatAndValidateRequest($taskList, $request);
            if (count($errors) > 0) {
                return new View(
                    $errors,
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if(!$this->checkAccess($taskList, Priority::CREATOR_PRIORITY)){
                throw new AccessDeniedException();
            }

            $this->persistAndFlush($taskList); 
            return $this->getSerializer($taskList, array("list_data"));
        }
        catch(NotFoundHttpException $e){
            return new View(
                ["error" => "Page not found."],
                Response::HTTP_NOT_FOUND);
        }
        catch(AccessDeniedException $e){
            return new View(
                ["error" => $e->getMessage()],
                Response::HTTP_FORBIDDEN);
        }        
    }

    public function deleteTasklistAction($id)
    {
        try {
            $taskList = $this
                ->getDoctrine()
                ->getRepository('ApiBundle:TaskList')
                ->find($id);
            if(!$taskList){
                throw new NotFoundHttpException();            
                }
            if(!$this->checkAccess($taskList, Priority::CREATOR_PRIORITY)){
                throw new AccessDeniedException();    
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($taskList);
            $em->flush();
            return;
        }
        catch(NotFoundHttpException $e){
            return new View(
                ["error" => "Page not found."],
                Response::HTTP_NOT_FOUND);
        }
        catch(AccessDeniedException $e){
            return new View(
                ["error" => $e->getMessage()],
                Response::HTTP_FORBIDDEN);
        }
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
        $priority->setPriority(Priority::CREATOR_PRIORITY);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($priority);
        $manager->flush();
        return;
    }

    private function getSerializer($tasklists, array $groups)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer));
        return $serializer->normalize($tasklists, null, array('groups' => $groups));
    }

}