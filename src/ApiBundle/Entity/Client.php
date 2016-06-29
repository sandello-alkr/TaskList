<?php
/**
 * Created by PhpStorm.
 * User: Uncle
 * Date: 10.06.2016
 * Time: 23:55
 */

namespace ApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups as UserGroups;
use FOS\OAuthServerBundle\Util\Random;

/**
 * @ORM\Table("oauth2_clients")
 * @ORM\Entity
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     */
    protected $randomId;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var array
     */
    protected $allowedGrantTypes = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRandomId()
    {
        return $this->randomId;
    }

    /**
     * @UserGroups({"client_data"})
     * {@inheritdoc}
     */
    public function getPublicId()
    {
        return sprintf('%s_%s', $this->getId(), $this->getRandomId());
    }

    /**
     * @UserGroups({"client_data"})
     * {@inheritdoc}
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @UserGroups({"client_data"})
     * {@inheritdoc}
     */
    public function getAllowedGrantTypes()
    {
        return $this->allowedGrantTypes;
    }
}
