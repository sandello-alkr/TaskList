<?php
/**
 * Created by PhpStorm.
 * User: Uncle
 * Date: 10.06.2016
 * Time: 23:54
 */

namespace ApiBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table("users")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\OneToMany(targetEntity="Priority", mappedBy="user", cascade={"persist", "remove"})
     */
    private $priorities;
     
    /**
     * Get priorities
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPriorities()
    {
        return $this->priorities;
    }

    public function __construct()
    {
        $this->priorities = new \Doctrine\Common\Collections\ArrayCollection();
    }
}