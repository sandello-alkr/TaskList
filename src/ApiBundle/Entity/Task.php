<?php
namespace ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\TaskRepository")
 */
class Task
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
     * @ORM\ManyToOne(targetEntity="TaskList")
     * @ORM\JoinColumn(name="task_list_id", referencedColumnName="id")
     */
    private $task_list;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;
    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;
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
     * @return Task
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
     * Set description
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    /**
     * Get description
     * @Groups({"list_data"})
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Set status
     * 
     * @param string $status
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    /**
     * Get status
     * @Groups({"list_data"})
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * Set task_list
     *
     * @param \ApiBundle\Entity\TaskList $taskList
     * @return Task
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