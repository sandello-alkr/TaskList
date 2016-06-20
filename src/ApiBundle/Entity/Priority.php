<?php
namespace ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Priority
 *
 * @ORM\Table(name="priority")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\PriorityRepository")
 */
class Priority
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
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;
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
     *
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
     *
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
     *
     * @return \ApiBundle\Entity\TaskList 
     */
    public function getTaskList()
    {
        return $this->task_list;
    }
}