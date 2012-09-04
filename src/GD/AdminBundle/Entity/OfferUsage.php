<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * GD\AdminBundle\Entity\OfferUsage
 *
 * @ORM\Table(name="offer_usage")
 * @ORM\Entity
 */
class OfferUsage
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
     * @var datetime $clickTime
     *
     * @ORM\Column(name="click_time", type="datetime")
     */
    private $clickTime;

    /**
     * @var integer $user
     * @ORM\ManyToOne(targetEntity="User", inversedBy="offerUsages")
     */
    private $user;

    /**
     * @var string $ip
     * 
     * @ORM\Column(name="ip", type="string", length=100)
     */
    private $ip;

    /**
     * @var integer $offer
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="offerUsages")
     */
    private $offer;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->offer = new ArrayCollection();
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
     * Set clickTime
     *
     * @param datetime $clickTime
     */
    public function setClickTime($clickTime)
    {
        $this->clickTime = $clickTime;
    }

    /**
     * Get clickTime
     *
     * @return datetime 
     */
    public function getClickTime()
    {
        return $this->clickTime;
    }

    /**
     * Set user
     *
     * @param integer $user
     */
    public function setUser($user)
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

    /**
     * Set ip
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set offer
     *
     * @param integer $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * Get offer
     *
     * @return integer 
     */
    public function getOffer()
    {
        return $this->offer;
    }
}