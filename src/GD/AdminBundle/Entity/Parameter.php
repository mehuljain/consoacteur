<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GD\AdminBundle\Entity\Parameter
 *
 * @ORM\Table(name="parameters")
 * @ORM\Entity
 */
class Parameter
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
     * @ORM\Column(name="collection_name", type="string", length=255)
     */
    private $collectionName;

    /**
     * @var string $parameterKey
     *
     * @ORM\Column(name="parameter_key", type="string", length=255)
     */
    private $parameterKey;

    /**
     * @var string $parameterValue
     *
     * @ORM\Column(name="parameter_value", type="string", length=255)
     */
    private $parameterValue;

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
     * Set collectionName
     *
     * @param string $collectionName
     */
    public function setCollectionName($collectionName)
    {
        $this->collectionName = $collectionName;
    }

    /**
     * Get collectionName
     *
     * @return string 
     */
    public function getCollectionName()
    {
        return $this->collectionName;
    }

    /**
     * Set parameterKey
     *
     * @param string $parameterKey
     */
    public function setParameterKey($parameterKey)
    {
        $this->parameterKey = $parameterKey;
    }

    /**
     * Get parameterKey
     *
     * @return string 
     */
    public function getParameterKey()
    {
        return $this->parameterKey;
    }

    /**
     * Set parameterValue
     *
     * @param string $parameterValue
     */
    public function setParameterValue($parameterValue)
    {
        $this->parameterValue = $parameterValue;
    }

    /**
     * Get parameterValue
     *
     * @return string 
     */
    public function getParameterValue()
    {
        return $this->parameterValue;
    }
}