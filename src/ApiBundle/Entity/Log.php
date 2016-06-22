<?php
namespace ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Log
 *
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\LogRepository")
 */
class Log
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
     * @var int
     *
     * @ORM\Column(name="logType", type="integer")
     */
    private $logType;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;
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
     * Set logType
     *
     * @param integer $logType
     * @return Log
     */
    public function setLogType($logType)
    {
        $this->logType = $logType;
        return $this;
    }
    /**
     * Get logType
     *
     * @return integer 
     */
    public function getLogType()
    {
        return $this->logType;
    }
    /**
     * Set description
     *
     * @param string $description
     * @return Log
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Set user
     *
     * @param \ApiBundle\Entity\User $user
     * @return Log
     */
    public function setUser(\ApiBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
    }
    /**
     * Set user
     *
     * @param \ApiBundle\Entity\User $user
     * @return Log
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
}