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
     * @Route("/reg", name="user_reg")
     * @Method("POST")
     */
    public function regAction(Request $request)
    {
        $user = new User();
        $errors = $this->treatAndValidateRequest($user, $request);
        if (count($errors) > 0)
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        if (empty($user->getUsername()) || empty($user->getEmail()) || empty($user->getPlainPassword()))
            return new View(
                ["error" => "fill all fields: username, email and plain_password"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $userManager = $this->get("fos_user.user_manager");
        if (!empty($userManager->findUserByUsername($user->getUsername())) ||
            !empty($userManager->findUserByEmail($user->getEmail())))
            return View::create(
                ["error" => "user with the same username or email exists"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $user->setEnabled(true);
        $user->setRoles(['ROLE_USER']);
        $userManager->updateUser($user);

        return View::create(
            ["message" => "user created"],
            Response::HTTP_CREATED
        );
    }

    /**
     * Client get.
     * @Route("/client", name="user_client")
     * @Method("GET")
     */
    public function clientAction()
    {
        $clientManager = $this->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setAllowedGrantTypes(['password']);
        $clientManager->updateClient($client);

        return View::create(
            [
                "client_id" => $client->getPublicId(),
                "client_secret" => $client->getSecret(),
                "grant_type" => $client->getAllowedGrantTypes()[0]
            ],
            Response::HTTP_OK
        );
    }

    /**
     * User get.
     * @Route("/{id}", name="user_get")
     * @Method("GET")
     * @ParamConverter("user", class="ApiBundle:User", options = {"mapping" : {"id" : "id"}})
     */
    public function getUserAction(User $user)
    {
        return View::create(
            [
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "email" => $user->getEmail()
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Users get.
     * @Route("s", name="users_get")
     * @Method("GET")
     */
    public function getUsersAction()
    {
        $users = $this
            ->getDoctrine()
            ->getRepository('ApiBundle:User')
            ->findAll();

        $users_public_data = array();
        foreach ($users as $user)
            $users_public_data[] = [
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "email" => $user->getEmail()
            ];

        return View::create(
            $users_public_data,
            Response::HTTP_OK
        );
    }

    /**
     * User edit.
     * @Route("/edit_password", name="user_edit_password")
     * @Method("PUT")
     */
    public function editUserAction(Request $request)
    {
        $user = $this->getUser();
        if (empty($request->get("new_password")))
            return new View(
                ["error" => "new password empty"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $user->setPlainPassword($request->get("new_password"));
        $user->setPassword($this
            ->get('security.encoder_factory')
            ->getEncoder($user)
            ->encodePassword($request->get("new_password"), $user->getSalt())
        );
        $this->persistAndFlush($user);

        return View::create(
            ["message" => "user password updated"],
            Response::HTTP_OK
        );
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

    private function persistAndFlush(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();
    }
}
