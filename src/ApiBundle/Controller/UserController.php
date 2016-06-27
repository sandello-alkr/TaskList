<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Client;
use ApiBundle\Entity\User;
use ApiBundle\Entity\ChangePassword;
use ApiBundle\Form\ChangePasswordType;
use ApiBundle\Form\UserType;
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
 * User controller.
 * @Route("/api/user")
 */
class UserController extends FOSRestController
{
    /**
     * User registration.
     * @Route("/reg", name="user_create")
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
    public function createUserAction(Request $request)
    {
        $user = new User();
        $errors = $this->treatAndValidateRequest($user, new UserType(), $request);
        if (count($errors) > 0)
            return View::create(
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
     * @Route("s", name="user_all")
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
     * @Route("", name="user_view")
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
     * @Route("", name="user_edit")
     * @Method("PUT")
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
     */
    public function editUserAction(Request $request)
    {
        $user = $this->getUser();
        $errors = $this->treatAndValidateRequest($user, new UserType(), $request);
        if (count($errors) > 0)
            return View::create(
                $errors,
                Response::HTTP_BAD_REQUEST
            );

        if (strcmp($this->encodePassword($user, $user->getPlainPassword()), $user->getPassword()) != 0)
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

    /**
     * User change password.
     * @Route("", name="user_change_password")
     * @Method("PATCH")
     * @ApiDoc(
     *  description="Edit Current Password",
     *  section="User",
     *  statusCodes = {
     *     Response::HTTP_OK = "Returned when password updated",
     *     Response::HTTP_BAD_REQUEST = "Returned when the form has errors",
     *     Response::HTTP_CONFLICT = "Returned when current password incorrect"
     *   },
     *  input={
     *      "class"="ApiBundle\Form\ChangePasswordType",
     *  }
     * )
     */
    public function changePasswordAction(Request $request)
    {
        $changePassword = new ChangePassword();
        $errors = $this->treatAndValidateRequest($changePassword, new ChangePasswordType(), $request);
        if (count($errors) > 0)
            return View::create(
                $errors,
                Response::HTTP_BAD_REQUEST
            );

        $user = $this->getUser();
        if (strcmp($this->encodePassword($user, $changePassword->getCurrentPassword()), $user->getPassword()) == 0){
            $userManager = $this->get("fos_user.user_manager");
            $user->setPlainPassword($changePassword->getNewPassword());
            $user->setPassword($this->encodePassword($user, $changePassword->getNewPassword()));
            $userManager->updateUser($user);

            return View::create(
                ["message" => "password updated"],
                Response::HTTP_OK
            );
        }

        return View::create(
            ["error" => "incorrect current password"],
            Response::HTTP_CONFLICT
        );
    }

    private function encodePassword(User $user, $password)
    {
        return $this
            ->get('security.encoder_factory')
            ->getEncoder($user)
            ->encodePassword($password, $user->getSalt());
    }

    private function getSerializer($users, array $groups)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer));

        return $serializer->normalize($users, null, array('groups' => $groups));
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
