<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Priority;
use ApiBundle\Entity\TaskList;
use ApiBundle\Entity\User;
use ApiBundle\Form\PriorityForm;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\Form;
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
use Symfony\Component\Validator\Validation;

/**
 * Priority controller.
 * @Route("/api/priority")
 */
class PriorityController extends FOSRestController
{
    /**
     * @Route("/addTask/{taskListName}", name="list_add")
     * @Method("POST")
     */
    public function addTaskEnyList($taskListName){
        $TaskList = new TaskList();
        $TaskList->setName($taskListName);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($TaskList);
        $manager->flush();
    }
    
    /**
     * @Route("/{id_user}/{id_taskList}/{new_priority}", name="prior_add")
     * @ParamConverter("user", class="ApiBundle:User", options = {"mapping" : {"id_user" : "id"}})
     * @ParamConverter("taskList", class="ApiBundle:TaskList", options = {"mapping" : {"id_taskList" : "id"}})
     * @Method("POST")
     * @ApiDoc(
     *  description="Create a new prior",
     *  section="Priority",
     *  statusCodes = {
     *   },
     *  input={
     *      "class"="ApiBundle\Form\PriorityForm",
     *  }
     * )
     */
    public function addPriority(Request $request, User $user, TaskList $taskList, $new_priority)
    {
        $newPriority = $new_priority;

        // получение всех приоритетов текущего пользователя для дальнейшей проверки на возможность изменения
        $currentUser = $this->getUser();
        $currentUserPriorities = $currentUser->getPriorities();

        //Создание объекта приоритета
        $priority = new Priority();
        $priority->setUser($user);
        $priority->setTaskList($taskList);
        $priority->setPriority($newPriority);


        // блок ниже - проверка прав текущаего пользователя на изменение/удаление/добавление приоритетов
        $accessAllow = false;
        foreach ($currentUserPriorities as $priorElement) {
            if($priority->getTaskList() == $priorElement->getTaskList()){  // наличие приоритета этому пользователю для текущего листа
                if(($priorElement->getPriority() == Priority::MODERATOR_PRIORITY)||($priorElement->getPriority() == Priority::CREATOR_PRIORITY)){ // доступный приоритет
                    $accessAllow = true;
                    break;
                }
            }
        }

        // в случае отсутствия прав или ошибки запроса - выход
       // $errors = $this->treatAndValidateRequest($priority, new PriorityForm(), $request);
        if ((!$accessAllow)||(Priority::CREATOR_PRIORITY == $newPriority))
            return View::create(
                $accessAllow,
                Response::HTTP_BAD_REQUEST
            );


        // добавление значений в базу
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($priority);
        $manager->flush();

        return Response::HTTP_OK;

    }

    /**
     * @Route("/{id_user}/{id_taskList}/{new_priority}", name="prior_update")
     * @ParamConverter("user", class="ApiBundle:User", options = {"mapping" : {"id_user" : "id"}})
     * @ParamConverter("taskList", class="ApiBundle:TaskList", options = {"mapping" : {"id_taskList" : "id"}})
     * @Method("PUT")
     * @ApiDoc(
     *  description="Update priority",
     *  section="Priority",
     *  statusCodes = {
     *   },
     *  input={
     *      "class"="ApiBundle\Form\PriorityForm",
     *  }
     * )
     */
    public function updateUserPriority(Reques1t $request, User $user, TaskList $taskList, $newPriority = 4)
    {
        $currentUser = $this->getUser();
        $currentUserPriorities = $currentUser->getPriorities();

        // работа с менеджером базы. Поиск и изменение объектов
        $manager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('ApiBundle:Priority');
        $priority = $repository->findOneBy(array('user' => $user->getId(), 'task_list' => $taskList->getId())); // ищем старые права изменяемому пользоватлю

        if(!$priority)  // отсутствие прав у пользователя, которого собираются повысить/понизить
            return View::create(
                $priority,
                Response::HTTP_NO_CONTENT
            );

        // блок ниже - проверка прав текущаего пользователя на изменение/удаление/добавление приоритетов
        $accessAllow = false;
        foreach ($currentUserPriorities as $priorElement) {
            if($taskList == $priorElement->getTaskList()){  // наличие приоритета  пользователя-изменятеля для текущего листа
                if(($priorElement->getPriority() == Priority::MODERATOR_PRIORITY)||($priorElement->getPriority() == Priority::CREATOR_PRIORITY)){ // доступный приоритет
                    $accessAllow = true;    // права есть у парня для изменения приоритета доступа других пользователей
                    break;
                }
            }
        }


        if ((!$accessAllow)||(Priority::CREATOR_PRIORITY == $newPriority)) // в случае отсутствия прав или попытки дать статус создателя листа
            return View::create(
                Response::HTTP_BAD_REQUEST
            );

        //!!!!
        $priority->setPriority($newPriority);
        $manager->flush();

        return $this->getSerializer($priority, array("prior_data"));

    }

    /**
     * @Route("/{id_user}/{id_taskList}", name="prior_delete")
     * @ParamConverter("user", class="ApiBundle:User", options = {"mapping" : {"id_user" : "id"}})
     * @ParamConverter("taskList", class="ApiBundle:TaskList", options = {"mapping" : {"id_taskList" : "id"}})
     * @Method("DELETE")
     * @ApiDoc(
     *  description="Delete user priority",
     *  section="Priority",
     *  statusCodes = {
     *   },
     *  input={
     *      "class"="ApiBundle\Form\PriorityForm",
     *  }
     * )
     */
    public function deletePriority(User $user, TaskList $taskList)
    {
        $currentUser = $this->getUser();
        $currentUserPriorities = $currentUser->getPriorities();

        $manager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('ApiBundle:Priority');
        $priority = $repository->findOneBy(array('user' => $user->getId(), 'task_list' => $taskList->getId()));

        $accessAllow = false;

        foreach ($currentUserPriorities as $priorElement) {
            if ($priority->getTaskList() == $priorElement->getTaskList()) {  // наличие приоритета этому пользователю для текущего листа
                if (($priorElement->getPriority() == Priority::MODERATOR_PRIORITY) || ($priorElement->getPriority() == Priority::CREATOR_PRIORITY)) { // доступный приоритет
                    $accessAllow = true;
                    break;
                }
            }
        }

        if (!$accessAllow)
            return View::create(
                Response::HTTP_NOT_MODIFIED
            );

        $manager->remove($priority);
        $manager->flush();
    }

    /**
     * User registration.
     * @Route("/{id_taskList}", name="get_priority")
     * @ParamConverter("taskList", class="ApiBundle:TaskList", options = {"mapping" : {"id_taskList" : "id"}})
     * @Method("GET")
     * @ApiDoc(
     *  description="get user priority",
     *  section="Priority",
     *  statusCodes = {
     *   },
     *  input={
     *      "class"="ApiBundle\Form\PriorityForm",
     *  }
     * )
     */
    public function getUserPriorityForTaskListByItId(TaskList $taskList)
    {
        $currentUser = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('ApiBundle:Priority');
        $priority = $repository->findOneBy(array('user' => $currentUser->getId(), 'task_list' => $taskList->getId()));
        return $this->getSerializer($priority, array("prior_data"));
    }

    /**
     * User registration.
     * @Route("/", name="get_all_priorities")
     * @Method("GET")
     * @ApiDoc(
     *  description="get user priority",
     *  section="Priority",
     *  statusCodes = {
     *   },
     *  input={
     *      "class"="ApiBundle\Form\PriorityForm",
     *  }
     * )
     */
    public function getAllUserPriorities()
    {
        $currentUser = $this->getUser();
        $currentUserPriorities = $currentUser->getPriorities();

        return $this->getSerializer($currentUserPriorities, array("prior_data"));

    }

    private function getSerializer($priorities, array $groups)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer));

        return $serializer->normalize($priorities, null, array('groups' => $groups));
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

}
