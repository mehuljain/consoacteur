<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GD\AdminBundle\Entity\Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="GD\AdminBundle\Repository\CategoryRepository")
 * @Gedmo\TranslationEntity(class="GD\AdminBundle\Entity\CategoryTranslation")
 */
class Category
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
     * @Gedmo\Translatable
     * @Gedmo\Sluggable(slugField="slug")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     * @Gedmo\Translatable
     */
    private $description;

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
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="categories")
     */
    protected $tags;

    /**
     * @ORM\ManyToMany(targetEntity="Merchant", mappedBy="categories")
     */
    protected $merchants;

    /**
     * @var string $slug
     * @Gedmo\Slug(updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    protected $slug;

    /**
     * @ORM\ManyToMany(targetEntity="Module", mappedBy="categories")
     */
    protected $modules;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->merchants = new ArrayCollection();
        $this->modules = new ArrayCollection();
    }

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
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
     * Set description
     *
     * @param text $description
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return Category
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
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
     * @return Category
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
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
     * @param $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add tags
     *
     * @param GD\AdminBundle\Entity\Tag $tags
     */
    public function addTag(\GD\AdminBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * Get tags
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
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
     * Add modules
     *
     * @param GD\AdminBundle\Entity\Module $modules
     */
    public function addModule(\GD\AdminBundle\Entity\Module $modules)
    {
        $this->modules[] = $modules;
    }

    /**
     * Get modules
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getModules()
    {
        return $this->modules;
    }
}