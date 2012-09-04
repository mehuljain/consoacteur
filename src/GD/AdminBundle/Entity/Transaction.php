<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GD\AdminBundle\Entity\Transaction
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="GD\AdminBundle\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Loggable
 */
class Transaction
{
    // Transaction Types
    const TRANSACTION_TYPE_REFERRAL = 1;
    const TRANSACTION_TYPE_OFFER = 2;
    const TRANSACTION_TYPE_JOINING = 3;
    
    // Transaction Offer Statuses
    const TRANSACTION_STATUS_WAITING = 1;
    const TRANSACTION_STATUS_PENDING_CONFIRMATION = 2;
    const TRANSACTION_STATUS_CONFIRMED = 3;
    const TRANSACTION_STATUS_PAYMENT_REQUESTED = 4;
    const TRANSACTION_STATUS_APPROVED = 5;
    const TRANSACTION_STATUS_PAID = 6;
    const TRANSACTION_STATUS_CANCELLED = 7;
    const TRANSACTION_STATUS_REJECTED = 8;
    const TRANSACTION_STATUS_ON_HOLD = 9;
    const TRANSACTION_STATUS_LOST = 10;
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float $userGainAmount
     * @Gedmo\Versioned
     * @ORM\Column(name="user_gain_amount", type="float", nullable=true)
     */
    private $userGainAmount;
    
    /**
     * @var float $commissionAmount
     * @Gedmo\Versioned
     * @ORM\Column(name="commission_amount", type="float", nullable=true)
     */
    private $commissionAmount;

    /**
     * @var float $transactionAmount
     * @Gedmo\Versioned
     * @ORM\Column(name="transaction_amount", type="float", nullable=true)
     */
    private $transactionAmount;

    /**
     * @var smallint $type
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var smallint $status
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var datetime $validationDate
     *
     * @ORM\Column(name="validation_date", type="datetime", nullable=true)
     */
    private $validationDate;

    /**
     * @var datetime $transactionDate
     *
     * @ORM\Column(name="transaction_date", type="datetime", nullable=true)
     */
    private $transactionDate;

    /**
     * @var datetime $confirmationDate
     *
     * @ORM\Column(name="confirmation_date", type="datetime", nullable=true)
     */
    private $confirmationDate;

    /**
     * @var datetime $merchantConfirmationDate
     *
     * @ORM\Column(name="merchant_confirmation_date", type="datetime", nullable=true)
     */
    private $merchantConfirmationDate;

    /**
     * @var datetime $merchantCancellationDate
     *
     * @ORM\Column(name="merchant_cancellation_date", type="datetime", nullable=true)
     */
    private $merchantCancellationDate;

    /**
     * @var datetime $rejectionDate
     *
     * @ORM\Column(name="rejection_date", type="datetime", nullable=true)
     */
    private $rejectionDate;

    /**
     * @var datetime $lostDate
     *
     * @ORM\Column(name="lost_date", type="datetime", nullable=true)
     */
    private $lostDate;
    
    /**
     * @var string $orderNumber
     *
     * @ORM\Column(name="order_number", type="string", nullable=true)
     */
    private $orderNumber;
    
    /**
     * @var string $reason
     *
     * @ORM\Column(name="reason", type="string", nullable=true)
     */
    private $reason;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var string $programId
     *
     * @ORM\Column(name="program_id", type="string", nullable=true)
     */
    private $programId;

    /**
     * @var smallint $offerType
     *
     * @ORM\Column(name="offer_type", type="smallint", nullable=true)
     */
    private $offerType;
    
    /**
     * @var boolean $isActionRequired
     *
     * @ORM\Column(name="is_action_required", type="boolean", nullable=true)
     */
    private $isActionRequired;

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
     * @var string $username
     * Used for mapping an extra field while exporting to XLS
     * @ORM\Column(name="username", type="string", nullable=true)
     */
     private $username;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="transactions")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="transactions")
     */
    private $offer;
    
    /**
     * @ORM\ManyToOne(targetEntity="Merchant", inversedBy="transactions")
     */
    private $merchant;

    /**
     * @ORM\ManyToOne(targetEntity="OfferUsage", inversedBy="transactions")
     */
    private $offerUsage;

    /**
     * @ORM\ManyToOne(targetEntity="Withdrawal", inversedBy="transactions")
     */
    private $withdrawal;
    
    /** 
     * @ORM\Version @ORM\Column(type="integer") 
     */
    private $version;
    
    /**
     *
     * @ORM\Column(name="referral_email", type="string", length=255, nullable=true)
     */
    private $referral_email;

    public function __construct($type = null, $amount = null, $user = null)
    {
        if ($type && $amount && $user) {
            $this->setType($type);
            $this->setAmount($amount);
            $this->setUser($user);
        }
    }
    
    public function __toString()
    {
        return "Transaction(".$this->id.")";
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
     * Set userGainAmount
     *
     * @param float $userGainAmount
     */
    public function setUserGainAmount($userGainAmount)
    {
        $this->userGainAmount = $userGainAmount;
    }

    /**
     * Get userGainAmount
     *
     * @return float 
     */
    public function getUserGainAmount()
    {
        return $this->userGainAmount;
    }

    /**
     * Set commissionAmount
     *
     * @param float $commissionAmount
     */
    public function setCommissionAmount($commissionAmount)
    {
        $this->commissionAmount = $commissionAmount;
    }

    /**
     * Get commissionAmount
     *
     * @return float 
     */
    public function getCommissionAmount()
    {
        return $this->commissionAmount;
    }

    /**
     * Set transactionAmount
     *
     * @param float $transactionAmount
     */
    public function setTransactionAmount($transactionAmount)
    {
        $this->transactionAmount = $transactionAmount;
    }

    /**
     * Get transactionAmount
     *
     * @return float 
     */
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
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

    public function getTypeAsString()
    {
        switch($this->type){
            case(self::TRANSACTION_TYPE_REFERRAL): return "Referral";
            case(self::TRANSACTION_TYPE_OFFER): return "Offer";
            case(self::TRANSACTION_TYPE_JOINING): return "Joining Bonus";
        }
    }

    public static function getTypeList()
    {
        return array(
            self::TRANSACTION_TYPE_REFERRAL => "Referral",
            self::TRANSACTION_TYPE_OFFER => "Offer",
            self::TRANSACTION_TYPE_JOINING => "Joining Bonus",
        );
    }

    /**
     * Set status
     *
     * @param smallint $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getStatusAsString()
    {
        switch($this->status) {
            case(self::TRANSACTION_STATUS_WAITING): return "Waiting";
            case(self::TRANSACTION_STATUS_PENDING_CONFIRMATION): return "Pending Confirmation";
            case(self::TRANSACTION_STATUS_CONFIRMED): return "Confirmed";
            case(self::TRANSACTION_STATUS_PAYMENT_REQUESTED): return "Payment Requested";
            case(self::TRANSACTION_STATUS_APPROVED): return "Payment Approved";
            case(self::TRANSACTION_STATUS_PAID): return "Paid";
            case(self::TRANSACTION_STATUS_CANCELLED): return "Cancelled";
            case(self::TRANSACTION_STATUS_REJECTED): return "Rejected";
            case(self::TRANSACTION_STATUS_ON_HOLD): return "On Hold";
            case(self::TRANSACTION_STATUS_LOST): return "Lost";
        }
    }

    /**
     * Used for form in backend
     */
    public static function getStatusList()
    {
        return array(
            self::TRANSACTION_STATUS_WAITING => "Waiting",
            self::TRANSACTION_STATUS_PENDING_CONFIRMATION => "Pending Confirmation",
            self::TRANSACTION_STATUS_CONFIRMED => "Confirmed",
            self::TRANSACTION_STATUS_PAYMENT_REQUESTED => "Payment Requested",
            self::TRANSACTION_STATUS_APPROVED => "Payment Approved",
            self::TRANSACTION_STATUS_PAID => "Paid",
            self::TRANSACTION_STATUS_CANCELLED => "Cancelled",
            self::TRANSACTION_STATUS_REJECTED => "Rejected",
            self::TRANSACTION_STATUS_ON_HOLD => "On Hold",
            self::TRANSACTION_STATUS_LOST => "Lost",
        );
    }

    /**
     * Set validationDate
     *
     * @param datetime $validationDate
     */
    public function setValidationDate($validationDate)
    {
        $this->validationDate = $validationDate;
    }

    /**
     * Get validationDate
     *
     * @return datetime 
     */
    public function getValidationDate()
    {
        return $this->validationDate;
    }

    /**
     * Set transactionDate
     *
     * @param datetime $transactionDate
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    /**
     * Get transactionDate
     *
     * @return datetime 
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * Set confirmationDate
     *
     * @param datetime $confirmationDate
     */
    public function setConfirmationDate($confirmationDate)
    {
        $this->confirmationDate = $confirmationDate;
    }

    /**
     * Get confirmationDate
     *
     * @return datetime 
     */
    public function getConfirmationDate()
    {
        return $this->confirmationDate;
    }

    /**
     * Set merchantConfirmationDate
     *
     * @param datetime $merchantConfirmationDate
     */
    public function setMerchantConfirmationDate($merchantConfirmationDate)
    {
        $this->merchantConfirmationDate = $merchantConfirmationDate;
    }

    /**
     * Get merchantConfirmationDate
     *
     * @return datetime 
     */
    public function getMerchantConfirmationDate()
    {
        return $this->merchantConfirmationDate;
    }

    /**
     * Set merchantCancellationDate
     *
     * @param datetime $merchantCancellationDate
     */
    public function setMerchantCancellationDate($merchantCancellationDate)
    {
        $this->merchantCancellationDate = $merchantCancellationDate;
    }

    /**
     * Get merchantCancellationDate
     *
     * @return datetime 
     */
    public function getMerchantCancellationDate()
    {
        return $this->merchantCancellationDate;
    }

    /**
     * Set rejectionDate
     *
     * @param datetime $rejectionDate
     */
    public function setRejectionDate($rejectionDate)
    {
        $this->rejectionDate = $rejectionDate;
    }

    /**
     * Get rejectionDate
     *
     * @return datetime 
     */
    public function getRejectionDate()
    {
        return $this->rejectionDate;
    }

    /**
     * Set lostDate
     *
     * @param datetime $lostDate
     */
    public function setLostDate($lostDate)
    {
        $this->lostDate = $lostDate;
    }

    /**
     * Get lostDate
     *
     * @return datetime 
     */
    public function getLostDate()
    {
        return $this->lostDate;
    }

    /**
     * Set orderNumber
     *
     * @param integer $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * Get orderNumber
     *
     * @return integer 
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set reason
     *
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
     * @return \GD\AdminBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set offer
     *
     * @param GD\AdminBundle\Entity\Offer $offer
     */
    public function setOffer(\GD\AdminBundle\Entity\Offer $offer)
    {
        $this->offer = $offer;
    }

    /**
     * Get offer
     *
     * @return \GD\AdminBundle\Entity\Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }
    
    /**
     * Set offerUsage
     *
     * @param GD\AdminBundle\Entity\OfferUsage $offerUsage
     */
    public function setOfferUsage(\GD\AdminBundle\Entity\OfferUsage $offerUsage)
    {
        $this->offerUsage = $offerUsage;
    }

    /**
     * Get offerUsage
     *
     * @return GD\AdminBundle\Entity\OfferUsage 
     */
    public function getOfferUsage()
    {
        return $this->offerUsage;
    }

    /**
     * Set withdrawal
     *
     * @param GD\AdminBundle\Entity\Withdrawal $withdrawal
     */
    public function setWithdrawal(\GD\AdminBundle\Entity\Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }
    
    public function unsetWithdrawal()
    {
        $this->withdrawal = null;
    }

    /**
     * Get withdrawal
     *
     * @return GD\AdminBundle\Entity\Withdrawal 
     */
    public function getWithdrawal()
    {
        return $this->withdrawal;
    }
    
    /**
     * @ORM\prePersist
     */
    public function updateTransactionValues()
    {
        // ON PRE PERSIST
    }

    /**
     * Set programId
     *
     * @param string $programId
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;
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
     * Set offerType
     *
     * @param integer $offerType
     */
    public function setOfferType($offerType)
    {
        $this->offerType = $offerType;
    }

    /**
     * Get offerType
     *
     * @return integer 
     */
    public function getOfferType()
    {
        return $this->offerType;
    }

    /**
     * Add withdrawals
     *
     * @param GD\AdminBundle\Entity\Withdrawal $withdrawals
     */
    public function addWithdrawal(\GD\AdminBundle\Entity\Withdrawal $withdrawals)
    {
        $this->withdrawals[] = $withdrawals;
    }

    /**
     * Get withdrawals
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getWithdrawals()
    {
        return $this->withdrawals;
    }

    /**
     * Set isActionRequired
     *
     * @param boolean $isActionRequired
     */
    public function setIsActionRequired($isActionRequired)
    {
        $this->isActionRequired = $isActionRequired;
    }

    /**
     * Get isActionRequired
     *
     * @return boolean 
     */
    public function getIsActionRequired()
    {
        return $this->isActionRequired;
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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Set version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
    /**
     * Get Version
     * 
     * @return integer
     */
    public function getVersion(){
      
      return $this->version;
    }
    
    /**
     * Set referral email
     *
     * @param string $version
     */
    public function setReferralEmail($referral_email)
    {
        $this->referral_email = $referral_email;
    }
    
    /**
     * Get referral email
     * 
     * @return string
     */
    public function getReferralEmail(){
      
      return $this->referral_email;
    }
}