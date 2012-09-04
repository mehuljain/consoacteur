<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GD\AdminBundle\Entity\Referral
 *
 * @ORM\Table(name="referrals")
 * @ORM\Entity(repositoryClass="GD\AdminBundle\Repository\ReferralRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Referral
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
     * @var string $referralEmail
     *
     * @ORM\Column(name="referral_email", type="string", length=255)
     */
    private $referralEmail;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="referrals")
     */
    private $user;

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
     * Set referralEmail
     *
     * @param string $referralEmail
     */
    public function setReferralEmail($referralEmail)
    {
        $this->referralEmail = $referralEmail;
    }

    /**
     * Get referralEmail
     *
     * @return string
     */
    public function getReferralEmail()
    {
        return $this->referralEmail;
    }

     /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param GD\AdminBundle\Entity\User $user
     */
    public function setUser(\GD\AdminBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __construct()
    {
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString()
    {
        return "Referral(".$this->id.")" ;
    }
}