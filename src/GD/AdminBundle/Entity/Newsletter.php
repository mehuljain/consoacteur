<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GD\AdminBundle\Entity\Newsletter
 *
 * @ORM\Table(name="newsletter")
 * @ORM\Entity(repositoryClass="GD\AdminBundle\Repository\NewsletterRepository")
 * 
 */
class Newsletter
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $email
     * 
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(message = "user.email.not_valid",groups={"newsletter"})
     * @Assert\NotBlank(message = "user.email.not_blank",groups={"newsletter"})
     */
    private $email;

    /**
     * @var boolean $isSubscribed
     *
     * @ORM\Column(name="is_subscribed", type="boolean")
     */
    private $isSubscribed;


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
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isSubscribed
     *
     * @param boolean $isSubscribed
     */
    public function setIsSubscribed($isSubscribed)
    {
        $this->isSubscribed = $isSubscribed;
    }

    /**
     * Get isSubscribed
     *
     * @return boolean 
     */
    public function getIsSubscribed()
    {
        return $this->isSubscribed;
    }
}