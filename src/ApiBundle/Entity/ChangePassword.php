<?php
/**
 * Created by PhpStorm.
 * User: Uncle
 * Date: 23.06.2016
 * Time: 17:27
 */

namespace ApiBundle\Entity;

use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Serializer\Annotation\Groups as UserGroups;

class ChangePassword
{
    /**
     * @var string
     * @Assert\Regex(
     *     pattern="/\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S/",
     *     message="Any set of characters. Length from 8 to 40. Containing at least one lowercase letter and at least one uppercase letter and at least one number."
     * )
     * @Assert\NotBlank
     */
    protected $newPassword;

    /**
     * @var string
     * @Assert\Regex(
     *     pattern="/\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S/",
     *     message="Any set of characters. Length from 8 to 40. Containing at least one lowercase letter and at least one uppercase letter and at least one number."
     * )
     * @Assert\NotBlank
     */
    protected $currentPassword;

    /**
     * @return string
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @return ChangePassword
     */
    public function setCurrentPassword($password)
    {
        $this->currentPassword = $password;

        return $this;
    }

    /**
     * @return ChangePassword
     */
    public function setNewPassword($password)
    {
        $this->newPassword = $password;

        return $this;
    }
}
