<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * TaskList
 *
 * @ORM\Table(name="task_list")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\TaskListRepository")
 */
class TaskList
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
     * @ORM\OneToMany(targetEntity="Task", mappedBy="task_list", cascade={"persist", "remove"})
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="Priority", mappedBy="task_list", cascade={"persist", "remove"})
     */
    private $priorities;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $name;

    /**
     * Get id
     * @Groups({"list_data"})
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
     * @return TaskList
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     * @Groups({"list_data"})
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add task
     *
     * @param \ApiBundle\Entity\Task $task
     */
    public function addTask(\ApiBundle\Entity\Task $task)
    {
        $this->tasks[] = $task;
        $task->setTaskList($this);
        return $this;
    }

    /**
     * Remove task
     *
     * @param \ApiBundle\Entity\Task $task
     */
    public function removeTask(\ApiBundle\Entity\Task $task)
    {
        $this->tasks->removeElement($task);
    }
     
    /**
     * Get tasks
     * @Groups({"list_data"})
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
