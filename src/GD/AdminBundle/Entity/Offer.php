<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GD\AdminBundle\Entity\Offer
 *
 * @ORM\Table(name="offers")
 * @ORM\Entity(repositoryClass="GD\AdminBundle\Repository\OfferRepository")
 * @Gedmo\TranslationEntity(class="GD\AdminBundle\Entity\OfferTranslation")
 * @Assert\Callback(methods={"isDateValid"})
 */
class Offer
{
    const OFFER_TYPE_CASHBACK = 1;
    const OFFER_TYPE_FULL_REIMBURSEMENT = 2;
    const OFFER_TYPE_CODE_PROMO = 3; // Can have multiple code promos at one point of time
    const OFFER_TYPE_SUBSCRIPTION_GAIN = 4;

    const SORT_BY_CASHBACK_VALUE = 1;
    const SORT_BY_CASHBACK_PERCENTAGE = 2;
    const SORT_BY_NAME = 3;
    const SORT_BY_START_DATE = 4;

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
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Gedmo\Translatable
     */
    private $description;

    /**
     * Only for Full reimbursement
     * @var text $fullReimbursementTermsAndConditions
     *
     * @ORM\Column(name="full_reimbursement_terms_and_conditions", type="text", nullable=true)
     * @Gedmo\Translatable
     */
    private $fullReimbursementTermsAndConditions;

    /**
     * @var smallint $type
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * For user reference
     * @var string $programId
     *
     * @ORM\Column(name="program_id", type="string", length=255)
     */
    private $programId;

    /**
     * @var string $redirectionUri
     *
     * @ORM\Column(name="redirection_uri", type="string", length=255)
     * @Assert\Url()
     */
    private $redirectionUri;

    /**
     * @var string $displayUri
     *
     * @ORM\Column(name="display_uri", type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $displayUri;

    /**
     * @var float $userGainPercentage
     *
     * @ORM\Column(name="user_gain_percentage", type="float", nullable=true)
     * @Assert\Min(limit = "0", message = "Percentage Value should not be less than 0")     
     * @Assert\Max(limit = "100", message = "User Gain Percentage Value should be less than 100")
     */
    private $userGainPercentage;

    /**
     * @var date $startDate
     *
     * @ORM\Column(name="start_date", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var date $endDate
     *
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var boolean $isCurrent
     *
     * @ORM\Column(name="is_current", type="boolean", nullable=true)
     */
    private $isCurrent;

    /**
     * @var boolean $isArchived
     *
     * @ORM\Column(name="is_archived", type="boolean")
     */
    private $isArchived;

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
     * @var datetime $archivedAt
     *
     * @ORM\Column(name="archived_at", type="datetime", nullable=true)
     */
    private $archivedAt;

    /**
     * @var float $fullReimbursementMaxWinAmount
     *
     * @ORM\Column(name="full_reimbursement_max_win_amount", type="float", nullable=true)
     */
    private $fullReimbursementMaxWinAmount;

    /**
     * @var integer $fullReimbursementMinParticipants
     *
     * @ORM\Column(name="full_reimbursement_min_participants", type="integer", nullable=true)
     */
    private $fullReimbursementMinParticipants;

    /**
     * @var float $fullReimbursementMinTransactionAmount
     *
     * @ORM\Column(name="full_reimbursement_min_transaction_amount", type="float", nullable=true)
     */
    private $fullReimbursementMinTransactionAmount;

    /**
     * @var float $fullReimbursementCashbackPercentage
     *
     * @ORM\Column(name="full_reimbursement_cashback_percentage", type="float", nullable=true)
     * @Assert\Min(limit = "0", message = "Percentage Value should not be less than 0")
     * @Assert\Max(limit = "100", message = "Percentage Value should not be more than 100")     
     */
    private $fullReimbursementCashbackPercentage;

    /**
     * @var float $cashbackValuePercentage
     *
     * @ORM\Column(name="cashback_value_percentage", type="float", nullable=true)
     * @Assert\Min(limit = "0", message = "Percentage Value should not be less than 0")
     * @Assert\Max(limit = "100", message = "Percentage Value should not be more than 100")
     */
    private $cashbackValuePercentage;

    /**
     * @var float $cashbackValueAmount
     *
     * @ORM\Column(name="cashback_value_amount", type="float", nullable=true)
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid {{ type }}.")
     */
    private $cashbackValueAmount;

    /**
     * @var string $userGainValue
     *
     * @ORM\Column(name="user_gain_value", type="string")
     * @Gedmo\Translatable
     */
    private $userGainValue;

    /**
     * @ORM\ManyToOne(targetEntity="Merchant", inversedBy="offers")
     * @ORM\JoinColumn(name="merchant_id", referencedColumnName="id")
     */
    protected $merchant;

    /**
     * @var string $slug
     * @Gedmo\Slug(updatable=true)
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
    /**
     * Used to display AffiliatePartner in Backend
     * @ORM\OneToOne(targetEntity="AffiliatePartner")
     */
    protected $affiliatePartner;

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }


    public function __construct()
    {
        $this->setStartDate(new \DateTime('today'));
        $this->setIsArchived(false);
        $this->setIsCurrent(true);
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
     * @return Offer
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
     * @return Offer
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
     * Set fullReimbursementTermsAndConditions
     *
     * @param text $fullReimbursementTermsAndConditions
     * @return Offer
     */
    public function setFullReimbursementTermsAndConditions($fullReimbursementTermsAndConditions)
    {
        $this->fullReimbursementTermsAndConditions = $fullReimbursementTermsAndConditions;
        return $this;
    }

    /**
     * Get fullReimbursementTermsAndConditions
     *
     * @return text
     */
    public function getFullReimbursementTermsAndConditions()
    {
        return $this->fullReimbursementTermsAndConditions;
    }

    /**
     * Set type
     *
     * @param smallint $type
     * @return Offer
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
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
    
    public function getTypeAsString()
    {
      
        switch($this->type){
            case(self::OFFER_TYPE_CASHBACK): return "Cashback";
            case(self::OFFER_TYPE_FULL_REIMBURSEMENT): return "Full Reimbursement";
            case(self::OFFER_TYPE_CODE_PROMO): return "Coupon Code";
            case(self::OFFER_TYPE_SUBSCRIPTION_GAIN): return "Subscription Gain";
        }
    }

    public static function getTypeList()
    {
        return array(
            self::OFFER_TYPE_CASHBACK => "Cashback",
            self::OFFER_TYPE_FULL_REIMBURSEMENT => "Full Reimbursement",
            self::OFFER_TYPE_CODE_PROMO => "Coupon Code",
            self::OFFER_TYPE_SUBSCRIPTION_GAIN => "Subscription Gain"
        );
    }

    /**
     * Set programId
     *
     * @param string $programId
     * @return Offer
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;
        return $this;
    }

    /**
     * Get programId
     *
     * @return string
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * Set redirectionUri
     *
     * @param string $redirectionUri
     * @return Offer
     */
    public function setRedirectionUri($redirectionUri)
    {
        $this->redirectionUri = $redirectionUri;
        return $this;
    }

    /**
     * Get redirectionUri
     *
     * @return string
     */
    public function getRedirectionUri()
    {
        return $this->redirectionUri;
    }

    /**
     * Set displayUri
     *
     * @param string $displayUri
     * @return Offer
     */
    public function setDisplayUri($displayUri)
    {
        $this->displayUri = $displayUri;
        return $this;
    }

    /**
     * Get displayUri
     *
     * @return string
     */
    public function getDisplayUri()
    {
        return $this->displayUri;
    }

    /**
     * Set userGainPercentage
     *
     * @param float $userGainPercentage
     * @return Offer
     */
    public function setUserGainPercentage($userGainPercentage)
    {
        $this->userGainPercentage = $userGainPercentage;
        return $this;
    }

    /**
     * Get userGainPercentage
     *
     * @return float
     */
    public function getUserGainPercentage()
    {
        return $this->userGainPercentage;
    }

    /**
     * Set startDate
     *
     * @param date $startDate
     * @return Offer
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Get startDate
     *
     * @return date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param date $endDate
     * @return Offer
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Get endDate
     *
     * @return date
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set isCurrent
     *
     * @param boolean $isCurrent
     * @return Offer
     */
    public function setIsCurrent($isCurrent)
    {
        $this->isCurrent = $isCurrent;
        return $this;
    }

    /**
     * Get isCurrent
     *
     * @return boolean
     */
    public function getIsCurrent()
    {
        return $this->isCurrent;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     * @return Offer
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
     * @return Offer
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
     * @return Offer
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
     * Set archivedAt
     *
     * @param datetime $archivedAt
     * @return Offer
     */
    public function setArchivedAt($archivedAt)
    {
        $this->archivedAt = $archivedAt;
        return $this;
    }

    /**
     * Get archivedAt
     *
     * @return datetime
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    /**
     * Set fullReimbursementMaxWinAmount
     *
     * @param float $fullReimbursementMaxWinAmount
     * @return Offer
     */
    public function setFullReimbursementMaxWinAmount($fullReimbursementMaxWinAmount)
    {
        $this->fullReimbursementMaxWinAmount = $fullReimbursementMaxWinAmount;
        return $this;
    }

    /**
     * Get fullReimbursementMaxWinAmount
     *
     * @return float
     */
    public function getFullReimbursementMaxWinAmount()
    {
        return $this->fullReimbursementMaxWinAmount;
    }

    /**
     * Set fullReimbursementMinParticipants
     *
     * @param integer $fullReimbursementMinParticipants
     * @return Offer
     */
    public function setFullReimbursementMinParticipants($fullReimbursementMinParticipants)
    {
        $this->fullReimbursementMinParticipants = $fullReimbursementMinParticipants;
        return $this;
    }

    /**
     * Get fullReimbursementMinParticipants
     *
     * @return integer
     */
    public function getFullReimbursementMinParticipants()
    {
        return $this->fullReimbursementMinParticipants;
    }

    /**
     * Set fullReimbursementMinTransactionAmount
     *
     * @param float $fullReimbursementMinTransactionAmount
     * @return Offer
     */
    public function setFullReimbursementMinTransactionAmount($fullReimbursementMinTransactionAmount)
    {
        $this->fullReimbursementMinTransactionAmount = $fullReimbursementMinTransactionAmount;
        return $this;
    }

    /**
     * Get fullReimbursementMinTransactionAmount
     *
     * @return float
     */
    public function getFullReimbursementMinTransactionAmount()
    {
        return $this->fullReimbursementMinTransactionAmount;
    }

    /**
     * Set fullReimbursementCashbackPercentage
     *
     * @param float $fullReimbursementCashbackPercentage
     * @return Offer
     */
    public function setFullReimbursementCashbackPercentage($fullReimbursementCashbackPercentage)
    {
        $this->fullReimbursementCashbackPercentage = $fullReimbursementCashbackPercentage;
        return $this;
    }

    /**
     * Get fullReimbursementCashbackPercentage
     *
     * @return float
     */
    public function getFullReimbursementCashbackPercentage()
    {
        return $this->fullReimbursementCashbackPercentage;
    }

    /**
     * Set cashbackValuePercentage
     *
     * @param float $cashbackValuePercentage
     * @return Offer
     */
    public function setCashbackValuePercentage($cashbackValuePercentage)
    {
        $this->cashbackValuePercentage = $cashbackValuePercentage;
        return $this;
    }

    /**
     * Get cashbackValuePercentage
     *
     * @return float
     */
    public function getCashbackValuePercentage()
    {
        return $this->cashbackValuePercentage;
    }

    /**
     * Set cashbackValueAmount
     *
     * @param float $cashbackValueAmount
     * @return Offer
     */
    public function setCashbackValueAmount($cashbackValueAmount)
    {
        $this->cashbackValueAmount = $cashbackValueAmount;
        return $this;
    }

    /**
     * Get cashbackValueAmount
     *
     * @return float
     */
    public function getCashbackValueAmount()
    {
        return $this->cashbackValueAmount;
    }

    /**
     * Set merchant
     *
     * @param GD\AdminBundle\Entity\Merchant $merchant
     * @return Offer
     */
    public function setMerchant(\GD\AdminBundle\Entity\Merchant $merchant = null)
    {
        $this->merchant = $merchant;
        return $this;
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

    public function setUserGainValue($userGainValue)
    {
        $this->userGainValue = $userGainValue;
    }

    public function getUserGainValue()
    {
        return $this->userGainValue;
    }

    /**
     * @param $slug
     * @return Offer
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

    public function getOfferUsages()
    {
        return $this->offerUsages;
    }

    /**
     * Add offerUsages
     *
     * @param GD\AdminBundle\Entity\OfferUsage $offerUsage
     */
    public function addOfferUsage(\GD\AdminBundle\Entity\OfferUsage $offerUsage)
    {
        $this->offerUsages[] = $offerUsage;
    }

    public function getTranslatableLocale()
    {
        return $this->locale;
    }
    
    public function isDateValid(ExecutionContext $context)
    {
        /*$now = new \DateTime();
        if ($this->getStartDate() < $now) {
            $propertyPath = $context->getPropertyPath() . '.startDate';
            $context->setPropertyPath($propertyPath);
            $context->addViolation('Start Date cannot be less than today.', array(), null);
        }
        */
        if ($this->getEndDate() < $this->getStartDate()) {
            $propertyPath = $context->getPropertyPath() . '.endDate';
            $context->setPropertyPath($propertyPath);
            $context->addViolation('End Date cannot be less than Start Date.', array(), null);
        }
    }
    
    /*
    public function validatePercentages(ExecutionContext $context)
    {
        if (self::OFFER_TYPE_CASHBACK == $this->getType()) {
            if (!(is_float($this->getCashbackValuePercentage())) || $this->getCashbackValuePercentage() > 100) {
                $propertyPath = $context->getPropertyPath() . '.cashbackValuePercentage';
                $context->setPropertyPath($propertyPath);
                $context->addViolation('Cashback Value Percentage should be a number less than 100', array(), null);
            }
        } 
        if (self::OFFER_TYPE_FULL_REIMBURSEMENT == $this->getType()) {
            if (!(is_float($this->getFullReimbursementCashbackPercentage())) || $this->getFullReimbursementCashbackPercentage() > 100) {
                $propertyPath = $context->getPropertyPath() . '.fullReimbursementCashbackPercentage';
                $context->setPropertyPath($propertyPath);
                $context->addViolation('Full Reimbursement Cashback Percentage should be a number less than 100', array(), null);
            }
        }
    }
    */
    
    /**
     * @return string
     */
    public function getAffiliatePartner()
    {
        return $this->affiliatePartner;
    }

    /**
     * Set affiliatePartner
     *
     * @param GD\AdminBundle\Entity\AffiliatePartner $affiliatePartner
     */
    public function setAffiliatePartner(\GD\AdminBundle\Entity\AffiliatePartner $affiliatePartner)
    {
        $this->affiliatePartner = $affiliatePartner;
    }

    public function resetId()
    {
        $this->id = null;
    }
}