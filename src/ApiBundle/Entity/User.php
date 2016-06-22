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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/^[a-z\d.]{5,40}$/i",
     *     message="Any set of characters. Length from 5 to 40."
     * )
     */
    protected $username;

    /**
     * @var string
     *
     * @Assert\Email()
     */
    protected $email;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     * @Assert\Regex(
     *     pattern="/\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S/",
     *     message="Any set of characters. Length from 8 to 40. Containing at least one lowercase letter and at least one uppercase letter and at least one number."
     * )
     * @Assert\NotBlank
     */
    protected $plainPassword;

    /**
     * Get id
     * @Groups({"user_data"})
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

    /**
     * @Groups({"user_data"})
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @Groups({"user_data"})
     */
    public function getEmail()
    {
        return $this->email;
    }
}
