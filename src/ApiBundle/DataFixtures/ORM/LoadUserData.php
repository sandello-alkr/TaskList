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
        $user->setPlainPassword('passW0rd');

        $user->setEnabled(true);
        $user->setRoles(array('ROLE_ADMIN'));

        $userManager->updateUser($user);

        $connection = $manager->getConnection();
        $connection->exec("INSERT INTO `oauth2_clients` VALUES (NULL, 'm1x3mkrnssg04k84wccwkoss0s4o48cgg0ok48ocgc8048w4c', 'a:0:{}', '39yb2i91dqw4w0wwggwsckwkogswssccw48gsosws4cog4o8os', 'a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}');");
    }
}
