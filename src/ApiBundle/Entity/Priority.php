<?php
namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups as UserGroups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
/**
 * Priority
 *
 * ORM\Table(name="priority", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"user", "task_list"})})
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\PriorityRepository")
 */
class Priority
{
    const CREATOR_PRIORITY      = 1;
    const MODERATOR_PRIORITY    = 2;
    const EXECUTOR_PRIORITY     = 3;
    const OBSERVER_PRIORITY     = 4;


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
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    /**
     * @ORM\ManyToOne(targetEntity="TaskList")
     * @ORM\JoinColumn(name="task_list_id", referencedColumnName="id")
     */
    private $task_list;
    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="[1-4]{1}",
     *     message="priority levels, ranging from 1 to 4"
     * )
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;
    /**
     * Get id
     * @UserGroups({"prior_data"})
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set priority
     *
     * @param integer $priority
     * @return Priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }
    /**
     * Get priority
     * @UserGroups({"prior_data"})
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }
    /**
     * Set user
     *
     * @param \ApiBundle\Entity\User $user
     * @return Priority
     */
    public function setUser(\ApiBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     * @UserGroups({"prior_data"})
     * @return \ApiBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Set task_list
     *
     * @param \ApiBundle\Entity\TaskList $taskList
     * @return Priority
     */
    public function setTaskList(\ApiBundle\Entity\TaskList $taskList = null)
    {
        $this->task_list = $taskList;
        return $this;
    }
    
    /**
     * Get task_list
     * @UserGroups({"prior_data"})
     * @return \ApiBundle\Entity\TaskList 
     */
    public function getTaskList()
    {
        return $this->task_list;
    }
}