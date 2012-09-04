<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GD\AdminBundle\Entity\Feedback
 *
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="GD\AdminBundle\Repository\FeedbackRepository")
 */
class Feedback
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
     * @var string $ipaddress
     *
     * @ORM\Column(name="ip_address", type="string", length=15)
     */
    private $ipAddress;

    /**
     * @var smallint $rating
     *
     * @ORM\Column(name="rating", type="smallint")
     */
    private $rating;

    /**
     * @var text $comment
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Merchant")
     * @ORM\JoinColumn(name="merchant_id", referencedColumnName="id")
     */
    protected $merchant;
    
    /**
     * @var boolean $isApproved
     *
     * @ORM\Column(name="is_approved", type="boolean")
     */
    private $isApproved;
    
    /**
     * @var boolean $isRejected
     *
     * @ORM\Column(name="is_rejected", type="boolean")
     */
    private $isRejected;
    
    public function __construct()
    {
        $this->setIsApproved(false);
        $this->setIsRejected(false);
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /* public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('rating', new NotBlank());
        $metadata->addPropertyConstraint('comment', new MinLength(50));
    } */



    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get ipAddress
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set rating
     *
     * @param smallint $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * Get rating
     *
     * @return smallint 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set comment
     *
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return text 
     */
    public function getComment()
    {
        return $this->comment;
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
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
     * @return GD\AdminBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set merchant
     *
     * @param GD\AdminBundle\Entity\Merchant $merchant
     */
    public function setMerchant(\GD\AdminBundle\Entity\Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Get merchant
     *
     * @return GD\AdminBundle\Entity\Merchant 
     */
    public function getMerchant()
    {
        return $this->merchant;
    }
    
    /**
     * Set isApproved
     *
     * @param boolean $isApproved
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;
    }

    /**
     * Get isApproved
     *
     * @return boolean
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }
    
    /**
     * Set isRejected
     *
     * @param boolean $isRejected
     */
    public function setIsRejected($isRejected)
    {
        $this->isRejected = $isRejected;
    }

    /**
     * Get isRejected
     *
     * @return boolean
     */
    public function getIsRejected()
    {
        return $this->isRejected;
    }
}