<?php

namespace GD\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use GD\AdminBundle\Entity\Transaction;

/**
 * GD\AdminBundle\Entity\Withdrawal
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Withdrawal
{
    const WITHDRAWAL_TYPE_BANK_1 = 1;
    const WITHDRAWAL_TYPE_BANK_2 = 2;
    const WITHDRAWAL_TYPE_CHEQUE = 3;
    const WITHDRAWAL_TYPE_PAYPAL = 4;

    // Withdrawal Statuses are same as Transaction Statuses

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $bankName
     *
     * @ORM\Column(name="bankName", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"bank_type2"})
     */
    private $bankName;

    /**
     * @var text $address
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     * @Assert\NotBlank(groups={"bank_type1", "bank_type2", "cheque"})
     */
    private $address;

    /**
     * @var string $country
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"bank_type2"})
     */
    private $country;

    /**
     * @var string $accountNumber
     *
     * @ORM\Column(name="accountNumber", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"bank_type2"})
     */
    private $accountNumber;

    /**
     * @var string $iban
     *
     * @ORM\Column(name="iban", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"bank_type1"})
     */
    private $iban;

    /**
     * @var string $swiftCode
     *
     * @ORM\Column(name="swiftCode", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"bank_type1"})
     */
    private $swiftCode;

    /**
     * @var string $chequePayee
     *
     * @ORM\Column(name="chequePayee", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"cheque"})
     */
    private $chequePayee;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\NotBlank( message = "user.email.not_blank",groups={"paypal"})
     * @Assert\Email(groups={"paypal"})
     */
    private $email;

    /**
     * @var float $amount
     *
     * @ORM\Column(name="amount", type="float")
     * @Assert\NotBlank(groups={"bank_type1", "bank_type2", "cheque","paypal"})
     */
    private $amount;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=255)
     * @Assert\NotBlank(groups={"bank_type1", "bank_type2", "cheque","paypal"})
     */
    private $code;

    /**
     * @var smallint $type
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var smallint $status
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var text $internalComment
     *
     * @ORM\Column(name="internal_comment", type="text", length=255, nullable=true)
     */
    private $internalComment;

    /**
     * @var text $user_Comment
     *
     * @ORM\Column(name="user_comment", type="text", length=255, nullable=true)
     */
    private $userComment;

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
     * @var datetime $validatedAt
     *
     * @ORM\Column(name="validated_at", type="datetime", nullable=true)
     */
    private $validatedAt;
    
    /**
     * @var datetime $requestedAt
     *
     * @ORM\Column(name="requested_at", type="datetime", nullable=true)
     */
    private $requestedAt;
    
    /**
     * @var datetime $processedAt
     *
     * @ORM\Column(name="processed_at", type="datetime", nullable=true)
     */
    private $processedAt;
    
    /**
     * @var datetime $paidAt
     *
     * @ORM\Column(name="paid_at", type="datetime", nullable=true)
     */
    private $paidAt;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="withdrawals")
     */
    private $user;
    
    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="withdrawal")
     */
    private $transactions;

    public function __construct($type = null, $amount = null, $user = null)
    {
        if ($type && $amount && $user) {
            $this->setType($type);
            $this->setAmount($amount);
            $this->setUser($user);
        }
        $this->setStatus(Transaction::TRANSACTION_STATUS_PAYMENT_REQUESTED);
        $this->transactions = new ArrayCollection();
    }

    public function __toString()
    {
        return "Withdrawal(".$this->getId().")";
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
     * Set bankName
     *
     * @param string $bankName
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
    }

    /**
     * Get bankName
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Set bankAddress
     *
     * @param text $bankAddress
     */
    public function setBankAddress($bankAddress)
    {
        $this->bankAddress = $bankAddress;
    }

    /**
     * Get address
     *
     * @return text
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set accountNumber
     *
     * @param string $accountNumber
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * Get accountNumber
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set iban
     *
     * @param string $iban
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set swiftCode
     *
     * @param string $swiftCode
     */
    public function setSwiftCode($swiftCode)
    {
        $this->swiftCode = $swiftCode;
    }

    /**
     * Get swiftCode
     *
     * @return string
     */
    public function getSwiftCode()
    {
        return $this->swiftCode;
    }

    /**
     * Set chequePayee
     *
     * @param string $chequePayee
     */
    public function setChequePayee($chequePayee)
    {
        $this->chequePayee = $chequePayee;
    }

    /**
     * Get chequePayee
     *
     * @return string
     */
    public function getChequePayee()
    {
        return $this->chequePayee;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set amount
     *
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
    
    public static function getWithdrawalTypeList()
    {
        return array(
            self::WITHDRAWAL_TYPE_BANK_1 => "Bank Type 1",
            self::WITHDRAWAL_TYPE_BANK_2 => "Bank Type 2",
            self::WITHDRAWAL_TYPE_CHEQUE => "Cheque",
            self::WITHDRAWAL_TYPE_PAYPAL => "Paypal",
        );
    }

    public function getTypeAsString()
    {
        switch($this->type) {
            case(self::WITHDRAWAL_TYPE_BANK_1): return "Bank Type 1";
            case(self::WITHDRAWAL_TYPE_BANK_2): return "Bank Type 2";
            case(self::WITHDRAWAL_TYPE_CHEQUE): return "Cheque";
            case(self::WITHDRAWAL_TYPE_PAYPAL): return "Paypal";
        }
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
        switch($this->status){
            case(Transaction::TRANSACTION_STATUS_WAITING): return "Waiting";
            case(Transaction::TRANSACTION_STATUS_PENDING_CONFIRMATION): return "Pending Confirmation";
            case(Transaction::TRANSACTION_STATUS_CONFIRMED): return "Confirmed";
            case(Transaction::TRANSACTION_STATUS_PAYMENT_REQUESTED): return "Payment Requested";
            case(Transaction::TRANSACTION_STATUS_APPROVED): return "Approved";
            case(Transaction::TRANSACTION_STATUS_PAID): return "Paid";
            case(Transaction::TRANSACTION_STATUS_CANCELLED): return "Cancelled";
            case(Transaction::TRANSACTION_STATUS_REJECTED): return "Rejected";
            case(Transaction::TRANSACTION_STATUS_ON_HOLD): return "On Hold";
            case(Transaction::TRANSACTION_STATUS_LOST): return "Lost";
        }
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
     * Set validatedAt
     *
     * @param datetime $validatedAt
     */
    public function setValidatedAt($validatedAt)
    {
        $this->validatedAt = $validatedAt;
    }

    /**
     * Get validatedAt
     *
     * @return datetime
     */
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }
    
    /**
     * Set requestedAt
     *
     * @param datetime $requestedAt
     */
    public function setRequestedAt($requestedAt)
    {
        $this->requestedAt = $requestedAt;
    }

    /**
     * Get requestedAt
     *
     * @return datetime
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }
    
    /**
     * Set processedAt
     *
     * @param datetime $processedAt
     */
    public function setProcessedAt($processedAt)
    {
        $this->processedAt = $processedAt;
    }

    /**
     * Get processedAt
     *
     * @return datetime
     */
    public function getProcessedAt()
    {
        return $this->processedAt;
    }
    
    /**
     * Set paidAt
     *
     * @param datetime $paidAt
     */
    public function setPaidAt($paidAt)
    {
        $this->paidAt = $paidAt;
    }

    /**
     * Get paidAt
     *
     * @return datetime
     */
    public function getPaidAt()
    {
        return $this->paidAt;
    }

    /**
     * Set address
     *
     * @param text $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * Set referrals
     *
     * @param GD\AdminBundle\Entity\Referral $referrals
     */
    public function setReferrals(\GD\AdminBundle\Entity\Referral $referrals)
    {
        $this->referrals = $referrals;
    }

    /**
     * Get referrals
     *
     * @return \GD\AdminBundle\Entity\Referrals
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * Add transactions
     *
     * @param GD\AdminBundle\Entity\Transaction $transactions
     */
    public function addTransaction(\GD\AdminBundle\Entity\Transaction $transactions)
    {
        $this->transactions[] = $transactions;
    }

    /**
     * Get transactions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Set internalComment
     *
     * @param string $internalComment
     */
    public function setInternalComment($internalComment)
    {
        $this->internalComment = $internalComment;
    }

    /**
     * Get internalComment
     *
     * @return string 
     */
    public function getInternalComment()
    {
        return $this->internalComment;
    }

    /**
     * Set userComment
     *
     * @param string $userComment
     */
    public function setUserComment($userComment)
    {
        $this->userComment = $userComment;
    }

    /**
     * Get userComment
     *
     * @return string 
     */
    public function getUserComment()
    {
        return $this->userComment;
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
}