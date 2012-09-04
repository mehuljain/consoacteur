<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GD\AdminBundle\Entity\Request
 *
 * @ORM\Table(name="requests")
 * @ORM\Entity(repositoryClass="GD\AdminBundle\Repository\RequestRepository")
 */
class Request
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
     * @var integer $user
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requests")
     */
    private $user;

    /**
     * @var integer $problemId
     * @ORM\Column(name="problem_id", type="integer")
     */
    private $problemId;

    /**
     * @var datetime $requestDate
     *
     * @ORM\Column(name="request_date", type="datetime")
     */
    private $requestDate;

    /**
     * @var string $subject
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var text $message
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

     
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
     * Set user
     *
     * @param integer $user
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

    /**
     * Set problemId
     *
     * @param integer $problemId
     */
    public function setProblemId($problemId)
    {
        $this->problemId = $problemId;
    }

    /**
     * Get problemId
     *
     * @return integer 
     */
    public function getProblemId()
    {
        return $this->problemId;
    }

    /**
     * Get problemId
     *
     * @return string 
     */
    public function getProblemTypeAsString()
    {
        $problemChoice = new \GD\SiteBundle\Form\Extension\ChoiceList\NewRequestChoiceList();
        $problemArray = $problemChoice->getChoices();
        return ($this->problemId === NULL) ? null : $problemArray[$this->problemId];
    }
    
    /**
     * Set requestDate
     *
     * @param datetime $requestDate
     */
     public function setRequestDate($requestDate)
     {
         $this->requestDate = $requestDate;
     }

    /**
     * Get requestDate
     *
     * @return datetime 
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param text $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return text 
     */
    public function getMessage()
    {
        return $this->message;
    }
    
}