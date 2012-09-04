<?php

namespace GD\AdminBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use GD\SiteBundle\Validator;
use GD\SiteBundle\Form\Extension\ChoiceList\GenderChoiceList;
/**
* @ORM\Entity
* @ORM\Table(name="users")
* @UniqueEntity(fields="username",message="user.username.not_unique",groups={"registration"})
* @UniqueEntity(fields="email",message = "user.email.not_unique", groups={"registration","profile"})
* @ORM\HasLifecycleCallbacks()
*/
class User extends BaseUser
{
  
    const ADVERTISEMENT_NONE = 0;
    const ADVERTISEMENT_MAXIMUM_1_PER_MONTH = 1;
    const ADVERTISEMENT_MAXIMUM_2_PER_MONTH = 2;
    const ADVERTISEMENT_MAXIMUM_4_PER_MONTH = 4;
    const ADVERTISEMENT_AS_NEEDED_TO_BE_BEST_INFORMED = 5;
    
    const REFERRAL_TYPE_FRIEND = 0;
    const REFERRAL_TYPE_REGISTRATION = 1;
    const REFERRAL_TYPE_GOOGLE = 2;
    const REFERRAL_TYPE_NEWSPAPER = 3;
    const REFERRAL_TYPE_AD = 4;
    
   /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="GD\AdminBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /**
     * @var string $username
     * 
     * @Assert\MinLength(
     *     limit=6,
     *     message="user.username.not_length",groups={"registration"})
     * @Assert\MaxLength(limit = 32, message="user.username.not_length",groups={"registration"})
     * @Assert\NotBlank( message = "user.username.not_blank",groups={"registration"})
     * @Assert\Regex(
     *      pattern="/[^a-zA-Z0-9-_.]/",
     *      match = false,
     *      message = "user.username.not_characters",
     *      groups={"registration"})
     */
    protected $username;
    
    /**
     * @var string $password
     * 
     * @Assert\MinLength(
     *     message="user.plainPassword.not_length",
     *     limit=4,
     *     groups={"registration","profile","resetpassword"})
     * @Assert\NotNull(message= "user.plainPassword.not_blank",groups={"registration", "resetpassword"})
     *  
     */
    protected $plainPassword;

    /**
     * @Assert\Email(message = "user.email.not_valid",groups={"registration"})
     * @Assert\NotBlank(message = "user.email.not_blank", groups={"registration"})
     */
    protected $email;

    /**
     * @var string $salutation
     *
     * @ORM\Column(name="salutation", type="string", length=20, nullable=true)
     * @Assert\NotNull(message="user.salutation.not_blank",groups={"registration","profile"})
     */
    protected $salutation;

    /**
     * @var string $firstName
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     * @Assert\Regex(
     *      pattern="/\d/",
     *      match=false,
     *      message="user.firstName.not_number",groups={"registration","profile"} )
     */
    protected $firstName;

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     * @Assert\Regex(
     *      pattern="/\d/",
     *      match=false,
     *      message= "user.lastName.not_number",groups={"registration","profile"} )
     */
    protected $lastName;

    /**
     * @var date $dateOfBirth
     *
     * @ORM\Column(name="dob", type="date", nullable=true)
     * @Assert\Date(message="user.dateOfBirth.not_valid",groups={"registration","profile"})
     */
    protected $dateOfBirth;

    /**
     * @var string $profession
     *
     * @ORM\Column(name="profession", type="string", nullable=true)
     */
    protected $profession;

    /**
     * @var integer $gender
     *
     * @ORM\Column(name="gender", type="integer", nullable=true)
     */
    protected $gender;

    /**
     * @var string $apartmentNumber
     *
     * @ORM\Column(name="apartment_number", type="string", nullable=true)
     */
    protected $apartmentNumber;

    /**
     * @var string $addressLocation
     *
     * @ORM\Column(name="address_location", type="string", nullable=true)
     */
    protected $addressLocation;

    /**
     * @var string $locationName
     *
     * @ORM\Column(name="location_name", type="string", nullable=true)
     */
    protected $locationName;

    /**
     * @var string $complementaryAddressDetails
     *
     * @ORM\Column(name="complementary_address_details", type="text", nullable=true)
     */
    protected $complementaryAddressDetails;

    /**
     * @var string $zipcode
     *
     * @ORM\Column(name="zipcode", type="string", nullable=true)
     */
    protected $zipcode;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    protected $city;

    /**
     * @var string $country
     *
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    protected $country;

    /**
     * @var string $phoneHome
     *
     * @ORM\Column(name="phone_home", type="string", nullable=true)
     */
    protected $phoneHome;

    /**
     * @var string $phoneMobile
     *
     * @ORM\Column(name="phone_mobile", type="string", nullable=true)
     */
    protected $phoneMobile;

    /**
     * @var string $phoneOffice
     *
     * @ORM\Column(name="phone_office", type="string", nullable=true)
     */
    protected $phoneOffice;

    /**
     * @var string $blacklistReason
     *
     * @ORM\Column(name="blacklist_reason", type="text", nullable=true)
     */
    protected $blacklistReason;
    
    /**
     * @var string $referralType
     *
     * @ORM\Column(name="referral_type", type="integer", nullable=true)
     */
    protected $referralType;

    /**
     * @var boolean $newsletterSubscription
     *
     * @ORM\Column(name="newsletter_subscription", type="boolean")
     */
    protected $newsletterSubscription;

    /**
     * @var boolean $shareContact
     *
     * @ORM\Column(name="share_contact", type="boolean", nullable=true)
     */
    protected $shareContact;

    /**
     * @var string $accountClosureReason
     *
     * @ORM\Column(name="account_closure_reason", type="text", nullable=true)
     */
    protected $accountClosureReason;

    /**
     * @var string $accountClosureComment
     *
     * @ORM\Column(name="account_closure_comment", type="text", nullable=true)
     */
    protected $accountClosureComment;

     /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="user")
     */
    protected $requests;

    /**
     * @var boolean $isAdminUser
     * @ORM\Column(name="is_admin_user", type="boolean")
     */
    protected $isAdminUser;
    
    /**
     * @Assert\NotBlank(message="user.hasAcceptedTermsAndConditions.not_blank",groups={"registration"})
     */
    protected $hasAcceptedTermsAndConditions;

    /**
     * @var datetime $archivedAt
     *
     * @ORM\Column(name="archived_at", type="datetime", nullable=true)
     */
    private $archivedAt;

    /**
     * @var boolean $isArchived
     *
     * @ORM\Column(name="is_archived", type="boolean")
     */
    private $isArchived;

    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="user")
     */
    protected $transactions;

    /**
     * @ORM\OneToMany(targetEntity="Withdrawal", mappedBy="user")
     */
    protected $withdrawals;

    /**
     * @var string $withdrawalCode
     *
     * @ORM\Column(name="withdrawal_code", type="string", nullable=true)
     */
    protected $withdrawalCode;

    /**
     * @var datetime $withdrawalCodeExpiresAt
     *
     * @ORM\Column(name="withdrawal_code_expires_at", type="datetime", nullable=true)
     */
    protected $withdrawalCodeExpiresAt;

    /**
     * @ORM\OneToMany(targetEntity="Referral", mappedBy="user")
     */
    protected $referrals;

    /**
     * @ORM\ManyToMany(targetEntity="Merchant")
     */
    protected $preferredMerchants;

    /**
     * @var boolean $isBlacklisted
     *
     * @ORM\Column(name="is_blacklisted", type="boolean")
     */
    protected $isBlacklisted;

    /**
     * @var datetime $blacklistedAt
     *
     * @ORM\Column(name="blacklisted_at", type="datetime", nullable=true)
     */
    protected $blacklistedAt;

    /**
     * @var string $sponsorshipCode
     *
     * @ORM\Column(name="sponsorship_code", type="string", nullable=true)
     * @Validator\CheckSponsor(message="user.sponsorshipCode.not_valid",property="username",groups={"registration"})
     */
    protected $sponsorshipCode;
    
    /**
     * @var integer $advertisementByEmail
     *
     * @ORM\Column(name="advertisement_by_email", type="integer")
     */
    protected $advertisementByEmail;
    
    /**
     * @var integer $advertisementByPost
     *
     * @ORM\Column(name="advertisement_by_post", type="integer")
     */
    protected $advertisementByPost;
    
    /**
     * @var integer $advertisementByTelephone
     *
     * @ORM\Column(name="advertisement_by_telephone", type="integer")
     */
    protected $advertisementByTelephone;
    
    /**
     * @var integer $advertisementBySms
     *
     * @ORM\Column(name="advertisement_by_sms", type="integer")
     */
    protected $advertisementBySms;
    
    /**
     * @var boolean $isOlduser
     *
     * @ORM\Column(name="is_olduser", type="boolean",nullable=true)
     */
    protected $isOlduser;

    public function __construct()
    {
        parent::__construct();

        $this->preferredMerchants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->withdrawals = new \Doctrine\Common\Collections\ArrayCollection();

        $this->setIsAdminUser(false);
        $this->setIsArchived(false);
        $this->setBlacklisted(false);
        $this->setNewsletterSubscription(false);
        $this->setShareContact(false);
        $this->setisOlduser(false);

        //$this->setSponsorshipCode($this->getUsername());
        $this->setAdvertisementByEmail(self::ADVERTISEMENT_MAXIMUM_4_PER_MONTH);
        $this->setAdvertisementByPost(self::ADVERTISEMENT_MAXIMUM_4_PER_MONTH);
        $this->setAdvertisementByTelephone(self::ADVERTISEMENT_MAXIMUM_4_PER_MONTH);
        $this->setAdvertisementBySms(self::ADVERTISEMENT_MAXIMUM_4_PER_MONTH);
    }

    public function __toString()
    {
        return $this->getUsername()."(".$this->getId().")";
    }

    public function getFullName()
    {
//        return utf8_encode($this->getFirstName().' '.$this->getLastName());
        return ($this->getFirstName().' '.$this->getLastName());
    }

    public function getAge()
    {
        if (!$this->getDateOfBirth()) {
            return 0;
        }

        $dateNow = new \DateTime();
        $diff = $dateNow->diff($this->getDateOfBirth());

        return $diff->y;
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
     * Set salutation
     *
     * @param string $salutation
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * Get salutation
     *
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }
    
    /**
     * Set firstName
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set dateOfBirth
     *
     * @param date $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * Get dateOfBirth
     *
     * @return date
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set profession
     *
     * @param string $profession
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @return integer
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Returns the gender string based on the values provided in the choicelist.
     * 
     * @return string gender
     */
    public function getGenderAsString()
    {
        $genderChoice = new GenderChoiceList();
        $genderArray = $genderChoice->getChoices();
        return ($this->gender === NULL) ? null : $genderArray[$this->gender];
    }
    
    /**
     * Set apartmentNumber
     *
     * @param string $apartmentNumber
     */
    public function setApartmentNumber($apartmentNumber)
    {
        $this->apartmentNumber = $apartmentNumber;
    }

    /**
     * Get apartmentNumber
     *
     * @return string
     */
    public function getApartmentNumber()
    {
        return $this->apartmentNumber;
    }

    /**
     * Set addressLocation
     *
     * @param string $addressLocation
     */
    public function setAddressLocation($addressLocation)
    {
        $this->addressLocation = $addressLocation;
    }

    /**
     * Get addressLocation
     *
     * @return string
     */
    public function getAddressLocation()
    {
        return $this->addressLocation;
    }

    /**
     * Set locationName
     *
     * @param string $locationName
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;
    }

    /**
     * Get locationName
     *
     * @return string
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * Set complementaryAddressDetails
     *
     * @param text $complementaryAddressDetails
     */
    public function setComplementaryAddressDetails($complementaryAddressDetails)
    {
        $this->complementaryAddressDetails = $complementaryAddressDetails;
    }

    /**
     * Get complementaryAddressDetails
     *
     * @return text
     */
    public function getComplementaryAddressDetails()
    {
        return $this->complementaryAddressDetails;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
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
     * Set phoneHome
     *
     * @param string $phoneHome
     */
    public function setPhoneHome($phoneHome)
    {
        $this->phoneHome = $phoneHome;
    }

    /**
     * Get phoneHome
     *
     * @return string
     */
    public function getPhoneHome()
    {
        return $this->phoneHome;
    }

    /**
     * Set phoneMobile
     *
     * @param string $phoneMobile
     */
    public function setPhoneMobile($phoneMobile)
    {
        $this->phoneMobile = $phoneMobile;
    }

    /**
     * Get phoneMobile
     *
     * @return string
     */
    public function getPhoneMobile()
    {
        return $this->phoneMobile;
    }

    /**
     * Set phoneOffice
     *
     * @param string $phoneOffice
     */
    public function setPhoneOffice($phoneOffice)
    {
        $this->phoneOffice = $phoneOffice;
    }

    /**
     * Get phoneOffice
     *
     * @return string
     */
    public function getPhoneOffice()
    {
        return $this->phoneOffice;
    }

    /**
     * Set referralType
     *
     * @param integer $referralType
     */
    public function setReferralType($referralType)
    {
        $this->referralType = $referralType;
    }

    /**
     * Get referralType
     *
     * @return integer
     */
    public function getReferralType()
    {
        return $this->referralType;
    }

    /**
     * Set newsletterSubscription
     *
     * @param boolean $newsletterSubscription
     */
    public function setNewsletterSubscription($newsletterSubscription)
    {
        $this->newsletterSubscription = $newsletterSubscription;
    }

    /**
     * Get newsletterSubscription
     *
     * @return boolean
     */
    public function getNewsletterSubscription()
    {
        return $this->newsletterSubscription;
    }

    /**
     * Set shareContact
     *
     * @param boolean $shareContact
     */
    public function setShareContact($shareContact)
    {
        $this->shareContact = $shareContact;
    }

    /**
     * Get shareContact
     *
     * @return boolean
     */
    public function getShareContact()
    {
        return $this->shareContact;
    }

    /**
     * Set accountClosureReason
     *
     * @param text $accountClosureReason
     */
    public function setAccountClosureReason($accountClosureReason)
    {
        $this->accountClosureReason = $accountClosureReason;
    }

    /**
     * Get accountClosureReason
     *
     * @return text
     */
    public function getAccountClosureReason()
    {
        return $this->accountClosureReason;
    }

    /**
     * Set accountClosureComment
     *
     * @param text $accountClosureComment
     */
    public function setAccountClosureComment($accountClosureComment)
    {
        $this->accountClosureComment = $accountClosureComment;
    }

    /**
     * Get accountClosureComment
     *
     * @return text
     */
    public function getAccountClosureComment()
    {
        return $this->accountClosureComment;
    }

    /**
     * Set isAdminUser
     *
     * @param boolean $isAdminUser
     */
    public function setIsAdminUser($isAdminUser)
    {
        $this->isAdminUser = $isAdminUser;
    }

    /**
     * Get isAdminUser
     *
     * @return boolean
     */
    public function getIsAdminUser()
    {
        return $this->isAdminUser;
    }

    /**
     * Add requests
     *
     * @param GD\AdminBundle\Entity\Request $requests
     */
    public function addRequest(\GD\AdminBundle\Entity\Request $requests)
    {
        $this->requests[] = $requests;
    }

    /**
     * Get requests
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRequests()
    {
        return $this->requests;
    }

    public function getHasAcceptedTermsAndConditions()
    {
        return $this->hasAcceptedTermsAndConditions;
    }

    public function setHasAcceptedTermsAndConditions($value)
    {
        $this->hasAcceptedTermsAndConditions = (boolean) $value;
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

    public function getGroups()
    {
        return parent::getGroups();
    }

    public function setGroups($groups)
    {
        parent::setGroups($groups);
    }

    /**
     * Add a group to the user groups.
     *
     * @param GroupInterface $group
     * @return User
     */
    public function addGroup(\FOS\UserBundle\Model\GroupInterface $group)
    {
        return parent::addGroup($group);
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
     * Set withdrawalCode
     *
     * @param string $withdrawalCode
     */
    public function setWithdrawalCode($withdrawalCode)
    {
        $this->withdrawalCode = $withdrawalCode;
    }

    /**
     * Get withdrawalCode
     *
     * @return string
     */
    public function getWithdrawalCode()
    {
        return $this->withdrawalCode;
    }

    /**
     * Set withdrawalCodeExpiresAt
     *
     * @param datetime $withdrawalCodeExpiresAt
     */
    public function setWithdrawalCodeExpiresAt($withdrawalCodeExpiresAt)
    {
        $this->withdrawalCodeExpiresAt = $withdrawalCodeExpiresAt;
    }

    /**
     * Get withdrawalCodeExpiresAt
     *
     * @return datetime
     */
    public function getWithdrawalCodeExpiresAt()
    {
        return $this->withdrawalCodeExpiresAt;
    }

    /**
     * Add referrals
     *
     * @param GD\AdminBundle\Entity\Referral $referrals
     */
    public function addReferrals(\GD\AdminBundle\Entity\Referral $referrals)
    {
        $this->referrals[] = $referrals;
    }

    /**
     * Get referrals
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * Add preferredMerchants
     *
     * @param GD\AdminBundle\Entity\Merchant $preferredMerchants
     */
    public function addPreferredMerchant(\GD\AdminBundle\Entity\Merchant $preferredMerchants)
    {
        $this->preferredMerchants[] = $preferredMerchants;
    }

    /**
     * Get preferredMerchants
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPreferredMerchants()
    {
        return $this->preferredMerchants;
    }
    
    public function removePreferedMerchant(\GD\AdminBundle\Entity\Merchant $preferredMerchants)
    {
        $this->preferredMerchants->removeElement($preferredMerchants);
    }

    public function setBlacklisted($isBlacklisted)
    {
        $this->isBlacklisted = $isBlacklisted;
    }

    public function isBlacklisted()
    {
        return $this->isBlacklisted;
    }

    public function setBlacklistedAt($blacklistedAt)
    {
        $this->blacklistedAt = $blacklistedAt;
    }

    public function getBlacklistedAt()
    {
        return $this->blacklistedAt;
    }

    /**
     * Set sponsorshipCode
     * 
     */
    public function setSponsorshipCode($sponsorshipCode)
    {
        $this->sponsorshipCode = $sponsorshipCode;
    }

    /**
     * Get sponsorshipCode
     *
     * @return string
     */
    public function getSponsorshipCode()
    {
        return $this->sponsorshipCode;
    }

    /**
     * Set advertisementByEmail
     *
     * @param integer $advertisementByType
     */
    public function setAdvertisementByEmail($advertisementByEmail)
    {
        $this->advertisementByEmail = $advertisementByEmail;
    }

    /**
     * Get advertisementByEmail
     *
     * @return integer
     */
    public function getAdvertisementByEmail()
    {
        return $this->advertisementByEmail;
    }

    /**
     * Set advertisementByPost
     *
     * @param integer $advertisementByPost
     */
    public function setAdvertisementByPost($advertisementByPost)
    {
        $this->advertisementByPost = $advertisementByPost;
    }

    /**
     * Get advertisementByPost
     *
     * @return integer
     */
    public function getAdvertisementByPost()
    {
        return $this->advertisementByPost;
    }
    
    /**
     * Set advertisementByTelephone
     *
     * @param integer $advertisementByTelephone
     */
    public function setAdvertisementByTelephone($advertisementByTelephone)
    {
        $this->advertisementByTelephone = $advertisementByTelephone;
    }

    /**
     * Get advertisementByTelephone
     *
     * @return integer
     */
    public function getAdvertisementByTelephone()
    {
        return $this->advertisementByTelephone;
    }
    
    /**
     * Set advertisementBySms
     *
     * @param integer $advertisementBySms
     */
    public function setAdvertisementBySms($advertisementBySms)
    {
        $this->advertisementBySms = $advertisementBySms;
    }

    /**
     * Get advertisementBySms
     *
     * @return integer
     */
    public function getAdvertisementBySms()
    {
        return $this->advertisementBySms;
    }

    /**
     * Set isBlacklisted
     *
     * @param boolean $isBlacklisted
     */
    public function setIsBlacklisted($isBlacklisted)
    {
        $this->isBlacklisted = $isBlacklisted;
    }

    /**
     * Get isBlacklisted
     *
     * @return boolean
     */
    public function getIsBlacklisted()
    {
        return $this->isBlacklisted;
    }

    /**
     * Add referrals
     *
     * @param GD\AdminBundle\Entity\Referral $referrals
     */
    public function addReferral(\GD\AdminBundle\Entity\Referral $referrals)
    {
        $this->referrals[] = $referrals;
    }

    /**
     * Add preferredMerchants
     *
     * @param GD\AdminBundle\Entity\Merchant $preferredMerchants
     */
    public function addMerchant(\GD\AdminBundle\Entity\Merchant $preferredMerchants)
    {
        $this->preferredMerchants[] = $preferredMerchants;
    }

    /**
     * Set blacklistReason
     *
     * @param string $blacklistReason
     */
    public function setBlacklistReason($blacklistReason)
    {
        $this->blacklistReason = $blacklistReason;
    }

    /**
     * Get blacklistReason
     *
     * @return string 
     */
    public function getBlacklistReason()
    {
        return $this->blacklistReason;
    }
    
    /**
     *  Get isOlduser
     *  @return boolean
     */
    public function getIsOlduser()
    {
      return $this->isOlduser;
    }    
    
    /**
     * Set isOlduser
     *
     * @param string $isOlduser
     */
    public function setisOlduser($isOlduser)
    {
        $this->isOlduser = $isOlduser;
    }
}