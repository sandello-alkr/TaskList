<?php
namespace ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Groups
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\GroupsRepository")
 */
class Groups
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     */
    private $creator;
    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="groups_users",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $users;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
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
     * Set name
     *
     * @param string $name
     * @return Groups
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set creator
     *
     * @param \ApiBundle\Entity\User $creator
     * @return Groups
     */
    public function setCreator(\ApiBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * Get creator
     *
     * @return \ApiBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Add users
     *
     * @param \ApiBundle\Entity\User $users
     * @return Groups
     */
    public function addUser(\ApiBundle\Entity\User $users)
    {
        $this->users[] = $users;
        return $this;
    }

    /**
     * Remove users
     *
     * @param \ApiBundle\Entity\User $users
     */
    public function removeUser(\ApiBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

