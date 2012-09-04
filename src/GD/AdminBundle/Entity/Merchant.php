<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * GD\AdminBundle\Entity\Merchant
 *
 * @ORM\Table(name="merchants")
 * @ORM\Entity(repositoryClass="GD\AdminBundle\Repository\MerchantRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="GD\AdminBundle\Entity\MerchantTranslation")
 */
class Merchant
{
    const MERCHANT_DEFAULT_AVERAGE_FEEDBACK = 3;
    const MERCHANT_DEFAULT_OFFER_MATURITY_PERIOD = 15;

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
     * @Gedmo\Sluggable(slugField="slug")
     * @ORM\Column(name="name", type="string", length=255)
     * @Gedmo\Translatable
     */
    private $name;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Gedmo\Translatable
     */
    private $title;

    /**
     * @var string $metaKeywords
     *
     * @ORM\Column(name="meta_keywords", type="string", length=1000, nullable=true)
     * @Gedmo\Translatable
     */
    private $metaKeywords;

    /**
     * @var string $metaDescription
     *
     * @ORM\Column(name="meta_description", type="string", length=1000, nullable=true)
     * @Gedmo\Translatable
     */
    private $metaDescription;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     * @Gedmo\Translatable
     */
    private $description;

    /**
     * @var string $keywords
     *
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true)
     * @Gedmo\Translatable
     */
    private $keywords;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @Assert\Image(maxSize="1M")
     */
    public $merchantImage;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_archived", type="boolean", nullable=true)
     */
    private $isArchived;

    /**
     * @var integer $offerMaturityPeriod
     * @ORM\Column(name="offer_maturity_period", type="integer", nullable=true)
     */
    private $offerMaturityPeriod;

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
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="archived_at", type="datetime", nullable=true)
     */
    private $archivedAt;

    /**
     * @var float $defaultAverageFeedback
     *
     * @ORM\Column(name="default_avg_feedback", type="float", nullable=true)
     * @Assert\Min(limit = "1", message = "Default Average Feedback should not be less than 1")
     * @Assert\Max(limit = "5", message = "Default Average Feedback should not be more than 5")
     */
    private $defaultAverageFeedback;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="merchants")
     */
    public $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="merchants")
     */
    public $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="merchants")
     */
    protected $primaryCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="merchants")
     */
    protected $primaryTag;

    /**
     * @var string $slug
     * @Gedmo\Slug(updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="merchant")
     */
    protected $offers;

    /**
     * @ORM\ManyToOne(targetEntity="AffiliatePartner", inversedBy="merchants")
     */
    protected $affiliatePartner;

    /**
      * @var smallint $cashbackSortOrder
      *
      * @ORM\Column(name="cashback_sort_order", type="smallint", nullable=true)
      */
     private $cashbackSortOrder;

    /**
      * @var smallint $codepromoSortOrder
      *
      * @ORM\Column(name="codepromo_sort_order", type="smallint", nullable=true)
      */
     private $codepromoSortOrder;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
     // a property used temporarily while deleting
    private $filenameForRemove;

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function __construct()
    {
        $this->setIsActive(false);
        $this->setIsArchived(false);
        $this->setDefaultAverageFeedback(self::MERCHANT_DEFAULT_AVERAGE_FEEDBACK);

        $this->setCashbackSortOrder(10);
        $this->setCodepromoSortOrder(10);
        $this->setOfferMaturityPeriod(self::MERCHANT_DEFAULT_OFFER_MATURITY_PERIOD);

        $this->offers = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName()."(".$this->getId().")";
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
     * @return Merchant
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
     * Set title
     *
     * @param string $title
     * @return Merchant
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return Merchant
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
     * Set keywords
     *
     * @param string $keywords
     * @return Merchant
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Merchant
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Merchant
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isArchived
     *
     * @param $isArchived
     * @return Merchant
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;
        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return Merchant
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
     * @return Merchant
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
     * Set updatedAt
     *
     * @param $archivedAt
     * @return Merchant
     */
    public function setArchivedAt($archivedAt)
    {
        $this->archivedAt = $archivedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    /**
     * Set affiliatePartner
     *
     * @param $affiliatePartner
     * @return Merchant
     */
    public function setAffiliatePartner($affiliatePartner)
    {
        $this->affiliatePartner = $affiliatePartner;
        return $this;
    }

    /**
     * Get affiliatePartner
     *
     * @return AffiliatePartner
     */
    public function getAffiliatePartner()
    {
        return $this->affiliatePartner;
    }

    /**
     * Set offerMaturityPeriod
     *
     * @param integer $offerMaturityPeriod
     * @return Merchant
     */
    public function setOfferMaturityPeriod($offerMaturityPeriod)
    {
        $this->offerMaturityPeriod = $offerMaturityPeriod;
        return $this;
    }

    /**
     * Get offerMaturityPeriod
     *
     * @return integer
     */
    public function getOfferMaturityPeriod()
    {
        return $this->offerMaturityPeriod;
    }

    public function getAbsolutePath()
    {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }

    public function getWebPath()
    {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved             
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }
    
    /**
     * 
     * @return string path where 
     */
    protected function getLiipMainDir($type)
    {
      return __DIR__ . '/../../../../web/' . $this->getUploadLiipDir($type);
    }

    protected function getUploadLiipDir($type)
    {
        if($type == 'sidebar')
        {
          return 'media/cache/sidebar/';
        }
        return 'media/cache/main/';
    }
    
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't spoil when displaying uploaded doc/image in the view.
        return 'uploads/merchants/images';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->merchantImage) {
            $this->image = $this->merchantImage->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->merchantImage) {
            return;
        }
        $liip_main = $this->getLiipMainDir('main').$this->id.'.'.$this->image;
        $liip_sidebar = $this->getLiipMainDir('sidebar').$this->id.'.'.$this->image;
        //First remove the cached images for the merchant if they exist
        if(file_exists($liip_main)){
              @unlink($liip_main);
        }
        if(file_exists($liip_sidebar)){
              @unlink($liip_sidebar);
        }
        
        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error         
         $this->merchantImage->move($this->getUploadRootDir(),  $this->id.'.'. $this->merchantImage->guessExtension());

        unset($this->merchantImage);
    }
    
    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
         $this->filenameForRemove = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($this->filenameForRemove) {
            unlink($this->filenameForRemove);
        }
    }

    public function addOffer(Offer $offer)
    {
        $this->offers[] = $offer;
    }

    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @param $slug
     * @return Merchant
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
     * Add categories
     *
     * @param \GD\AdminBundle\Entity\Category $category
     */
    public function addCategory(\GD\AdminBundle\Entity\Category $category)
    {
        $category->addMerchant($this); // synchronously updating inverse side
        $this->categories[] = $category;
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add tags
     *
     * @param \GD\AdminBundle\Entity\Tag $tag
     */
    public function addTag(\GD\AdminBundle\Entity\Tag $tag)
    {
        $tag->addMerchant($this); // synchronously updating inverse side
        $this->tags[] = $tag;
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set primaryCategory
     *
     * @param GD\AdminBundle\Entity\Category $primaryCategory
     */
    public function setPrimaryCategory(\GD\AdminBundle\Entity\Category $primaryCategory)
    {
        $this->primaryCategory = $primaryCategory;
    }

    /**
     * Get primaryCategory
     *
     * @return GD\AdminBundle\Entity\Category
     */
    public function getPrimaryCategory()
    {
        return $this->primaryCategory;
    }

    /**
     * Set primaryTag
     *
     * @param GD\AdminBundle\Entity\Tag $primaryTag
     */
    public function setPrimaryTag(\GD\AdminBundle\Entity\Tag $primaryTag)
    {
        $this->primaryTag = $primaryTag;
    }

    /**
     * Get primaryTag
     *
     * @return GD\AdminBundle\Entity\Tag
     */
    public function getPrimaryTag()
    {
        return $this->primaryTag;
    }

    /**
     * Set cashbackSortOrder
     *
     * @param smallint $cashbackSortOrder
     */
    public function setCashbackSortOrder($cashbackSortOrder)
    {
        $this->cashbackSortOrder = $cashbackSortOrder;
    }

    /**
     * Get cashbackSortOrder
     *
     * @return smallint
     */
    public function getCashbackSortOrder()
    {
        return $this->cashbackSortOrder;
    }

    /**
     * Set codepromoSortOrder
     *
     * @param smallint $codepromoSortOrder
     */
    public function setCodepromoSortOrder($codepromoSortOrder)
    {
        $this->codepromoSortOrder = $codepromoSortOrder;
    }

    /**
     * Get codepromoSortOrder
     *
     * @return smallint
     */
    public function getCodepromoSortOrder()
    {
        return $this->codepromoSortOrder;
    }

    public function getTranslatableLocale()
    {
        return $this->locale;
    }
    /**
     * Set metaKeywords
     *
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * Get metaKeywords
     *
     * @return string 
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * Get metaDescription
     *
     * @return string 
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set defaultAverageFeedback
     *
     * @param float $defaultAverageFeedback
     */
    public function setDefaultAverageFeedback($defaultAverageFeedback)
    {
        $this->defaultAverageFeedback = $defaultAverageFeedback;
    }

    /**
     * Get defaultAverageFeedback
     *
     * @return float 
     */
    public function getDefaultAverageFeedback()
    {
        return $this->defaultAverageFeedback;
    }

    /**
     * For backend duplicate functionality. 
     */
    public function resetId()
    {
        $this->id = null;
    }

    public function resetPrimaryCategory()
    {
        $this->primaryCategory = null;
    }

    public function resetPrimaryTag()
    {
        $this->primaryTag = null;
    }
}