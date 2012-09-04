<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GD\AdminBundle\Entity\MerchantList
 *
 * @ORM\Table(name="merchant_list")
 * @ORM\Entity
 */
class MerchantList
{
    const LIST_TYPE_NORMAL = 1;
    const LIST_TYPE_CAROUSEL = 2;

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
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var smallint $type
     *
     * @ORM\Column(name="type", type="smallint")
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="Merchant")
     * @ORM\OrderBy({"cashbackSortOrder" = "ASC"})
     */
    protected $merchants;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->merchants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->type = self::LIST_TYPE_NORMAL;
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
     * Set type
     *
     * @param smallint $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return smallint
     */
    public function getType()
    {
        return $this->type;
    }

    public function updateSortOrder($sortOrder)
    {
        $order = array_keys($sortOrder);
        foreach ($this->getMerchants() as $m) {
            if ($this->getType() == self::LIST_TYPE_CAROUSEL) {
                //check if the key exists
                $key = array_search($m->getId(), $order);
                if ($key === false) {
                    continue;
                }

                if ('cashback-merchants' == $this->getName()) {
                        $m->setCashbackSortOrder($key);
                } elseif ('codepromo-merchants' == $this->getName()) {
                        $m->setCodepromoSortOrder($key);
                }
            }
        }
    }
}