<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Client;
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

use ApiBundle\Entity\User;
use ApiBundle\Form\UserType;

/**
 * User controller.
 * @Route("/api/user")
 */
class UserController extends FOSRestController
{
    /**
     * User registration.
     * @Route("/create", name="user_create")
     * @Method("POST")
     * @ApiDoc(
     *  description="Create a new User",
     *  section="User",
     *  statusCodes = {
     *     Response::HTTP_CREATED = "Returned when user created",
     *     Response::HTTP_BAD_REQUEST = "Returned when the form has errors",
     *     Response::HTTP_CONFLICT = "Returned when user with the same username or email exists"
     *   },
     *  input={
     *      "class"="ApiBundle\Form\UserType",
     *  }
     * )
     */
    public function postCreateAction(Request $request)
    {
        $user = new User();
        $errors = $this->treatAndValidateRequest($user, $request);
        if (count($errors) > 0)
            return new View(
                $errors,
                Response::HTTP_BAD_REQUEST
            );

        $userManager = $this->get("fos_user.user_manager");
        $user->setEnabled(true);
        $user->setRoles(['ROLE_USER']);

        try {
            $userManager->updateUser($user);

            return View::create(
                ["message" => "user created"],
                Response::HTTP_CREATED
            );
        } catch(\Exception $e){
            return View::create(
                ["error" => "user with the same username or email exists"],
                Response::HTTP_CONFLICT
            );
        }
    }

    /**
     * Users get.
     * @Route("/all", name="user_all")
     * @Method("GET")
     * @ApiDoc(
     *  description="Get All Users",
     *  section="User",
     *  statusCodes = {
     *     Response::HTTP_OK = "Returned when users received",
     *   },
     *  output={
     *      "class"="ApiBundle\Entity\User",
     *  }
     * )
     */
    public function getUsersAction()
    {
        $users = $this
            ->getDoctrine()
            ->getRepository('ApiBundle:User')
            ->findAll();

        return View::create(
            $this->getSerializer($users, array("user_data")),
            Response::HTTP_OK
        );
    }

    /**
     * Current user get.
     * @Route("/view", name="user_view")
     * @Method("GET")
     * @ApiDoc(
     *  description="Get current User",
     *  section="User",
     *  statusCodes = {
     *     Response::HTTP_OK = "Returned when current user received",
     *   },
     *  output={
     *      "class"="ApiBundle\Entity\User",
     *  }
     * )
     */
    public function getCurrentUserAction()
    {
        return View::create(
            $this->getSerializer($this->getUser(), array("user_data")),
            Response::HTTP_OK
        );
    }

    /*
     * Client get.
     * @Route("/client", name="user_client")
     * @Method("GET")
     */
    /*public function getClientAction()
    {
        $clientManager = $this->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setAllowedGrantTypes(['password', 'refresh_token']);
        $clientManager->updateClient($client);

        return View::create(
            $this->getSerializer($client, array("client_data")),
            Response::HTTP_OK
        );
    }*/

    /**
     * User get.
     * @Route("/{id}", name="user_get")
     * @Method("GET")
     * @ParamConverter("user", class="ApiBundle:User", options = {"mapping" : {"id" : "id"}})
     * @ApiDoc(
     *  description="Get user on id",
     *  section="User",
     *  statusCodes = {
     *     Response::HTTP_OK = "Returned when user received on id",
     *   },
     *  output={
     *      "class"="ApiBundle\Entity\User",
     *  }
     * )
     */
    public function getUserAction(User $user)
    {
        return View::create(
            $this->getSerializer($user, array("user_data")),
            Response::HTTP_OK
        );
    }

    /**
     * User edit.
     * @Route("/edit", name="user_edit")
     * @Method("POST")
     * @ApiDoc(
     *  description="Edit Current User",
     *  section="User",
     *  statusCodes = {
     *     Response::HTTP_OK = "Returned when user updated",
     *     Response::HTTP_BAD_REQUEST = "Returned when the form has errors",
     *     Response::HTTP_CONFLICT = "Returned when user with the same username or email exists"
     *   },
     *  input={
     *      "class"="ApiBundle\Form\UserType",
     *  }
     * )
     *
     */
    public function postUserAction(Request $request)
    {
        $user = $this->getUser();
        $errors = $this->treatAndValidateRequest($user, $request);
        if (count($errors) > 0)
            return new View(
                $errors,
                Response::HTTP_BAD_REQUEST
            );

        $encode_password = $this
            ->get('security.encoder_factory')
            ->getEncoder($user)
            ->encodePassword($user->getPlainPassword(), $user->getSalt());

        if (strcmp($encode_password, $user->getPassword()) != 0)
            return View::create(
                ["error" => "incorrect password"],
                Response::HTTP_CONFLICT
            );

        $userManager = $this->get("fos_user.user_manager");

        try {
            $userManager->updateUser($user);

            return View::create(
                ["message" => "user updated"],
                Response::HTTP_OK
            );
        } catch(\Exception $e){
            return View::create(
                ["error" => "user with the same username or email exists"],
                Response::HTTP_CONFLICT
            );
        }
    }

    private function getSerializer($users, array $groups)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer));

        return $serializer->normalize($users, null, array('groups' => $groups));
    }

    private function treatAndValidateRequest(User $user, Request $request)
    {
        $form = $this->createForm(
            new UserType(),
            $user,
            [
                'method' => $request->getMethod()
            ]
        );

        $form->handleRequest($request);
        $errors = $this->get('validator')->validate($user);

        return $errors;
    }
}
