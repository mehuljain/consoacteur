<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * GD\AdminBundle\Entity\AffiliatePartner
 *
 * @ORM\Table(name="affiliate_partners")
 * @ORM\Entity
 * @UniqueEntity("codename")
 */
class AffiliatePartner
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=60)
     */
    private $name;

    /**
     * @var string $codename
     *
     * @ORM\Column(name="codename", type="string", length=60, unique=true)
     */
    private $codename;
    
    /**
     * @ORM\OneToMany(targetEntity="Merchant", mappedBy="affiliatePartner")
     */
    protected $merchants;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    public function __toString()
    {
        return $this->getName();
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

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set codename
     *
     * @param string $codename
     */
    public function setCodename($codename)
    {
        $this->codename = $codename;
    }

    /**
     * Get codename
     *
     * @return string 
     */
    public function getCodename()
    {
        return $this->codename;
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
    public function __construct()
    {
        $this->merchants = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add merchants
     *
     * @param GD\AdminBundle\Entity\Merchant $merchants
     */
    public function addMerchant(\GD\AdminBundle\Entity\Merchant $merchants)
    {
        $this->merchants[] = $merchants;
    }

    /**
     * Get merchants
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMerchants()
    {
        return $this->merchants;
    }
}