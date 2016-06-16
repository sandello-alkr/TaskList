<?php
/**
 * Created by PhpStorm.
 * User: Uncle
 * Date: 16.06.2016
 * Time: 15:40
 */

namespace ApiBundle\DataFixtured\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface,  ContainerAwareInterface
{
    protected $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('admin');
        $user->setEmail('admin@example.com');
        $user->setPlainPassword('admin');
        
        //$user->setPassword('3NCRYPT3D-V3R51ON');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_ADMIN'));

        $userManager->updateUser($user, true);

        $connection = $manager->getConnection();
        $connection->exec("INSERT INTO `oauth2_clients` VALUES (NULL, '3bcbxd9e24g0gk4swg0kwgcwg4o8k8g4g888kwc44gcc0gwwk4', 'a:0:{}', '4ok2x70rlfokc8g0wws8c8kwcokw80k44sg48goc0ok4w0so0k', 'a:1:{i:0;s:8:\"password\";}');");
    }
}

