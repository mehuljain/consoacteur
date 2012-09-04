<?php

namespace GD\SiteBundle\Listener;

use GD\AdminBundle\Entity\User;
use GD\AdminBundle\Entity\Transaction;
use GD\AdminBundle\Entity\Offer;
use GD\AdminBundle\Entity\Referral;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Monolog\Logger;
use Zend\Search\lib\SearchUtility;

class postPersistListener
{
    protected $joiningBonus;
    protected $logger;
    protected $referralBonus;
    protected $locale;
    protected $router;
    
    /**
     * The post persist listener class.
     * 
     * @param Container $container
     * @param float $joiningBonus
     * @param float $referralBonus
     * @param Logger $logger
     * @param \Symfony\Component\Routing\Router $router 
     */
    
    public function __construct(Container $container, $joiningBonus, $referralBonus, Logger $logger,\Symfony\Component\Routing\Router $router)
    {
        $this->joiningBonus = $joiningBonus;
        $this->referralBonus = $referralBonus;
        $this->logger = $logger;
        $this->router = $router;
    }

    /**
     *  This function adds joining bonus to the user's account on sucessful registration.
     *
     * @param object $em The entity manager object
     * @param object $user The user object
     * @return void 
     */
    private function addJoiningBonus($em, $user)
    {
        if ($user->getIsAdminUser()) {
            return;
        }
        
        $transaction = new Transaction();
        $transaction->setType(Transaction::TRANSACTION_TYPE_JOINING);
        $transaction->setStatus(Transaction::TRANSACTION_STATUS_CONFIRMED);
        $transaction->setUserGainAmount($this->joiningBonus);
        $transaction->setUser($user);

        try{
            $em->persist($transaction);
            $em->flush();
        } catch (\Exception $e) {
            // Search for these errors using app.ERROR in dev.log or prod.log
            $this->logger->err('****ERROR**** Joining Bonus could not be added to the User with ID:'.$user->getId());
        }
    }
    
    /**
     *  This function adds referral bonus to the user's account on sucessful registration.
     *
     * @param object $em The entity manager object
     * @param object $user The user object
     * @return void 
     */
    private function addReferralBonus($em, $user)
    {
        if ($user->getIsAdminUser() || !$user->getSponsorshipCode()) {
            return;
        }
        
        $gainUser = $em->getRepository('GDAdminBundle:User')->findOneBy(array('username' => $user->getSponsorshipCode()));

        $transaction = new Transaction();
        $transaction->setType(Transaction::TRANSACTION_TYPE_REFERRAL);
        $transaction->setStatus(Transaction::TRANSACTION_STATUS_PENDING_CONFIRMATION);
        
        $transaction->setUserGainAmount($this->referralBonus);
        $transaction->setUser($gainUser);
        $transaction->setReferralEmail($user->getEmail());

        try{
            $em->persist($transaction);
            $em->flush();
        } catch (\Exception $e) {
            // Search for these errors using app.ERROR in dev.log or prod.log
            $this->logger->err('****ERROR**** Referral Bonus could not be added to the User with ID:'.$user->getId());
        }
    }
    
    /**
     * In case the backend user creates multiple cashback, full reimbursement or subscription gain offer for a single merchant,
     * this function ensures that the corresponding old offer for this merchant is not currrent. 
     * 
     * @param object $em The entity manager object
     * @param Offer $offer The offer entity object.
     * @return void 
     */

    private function setIsActiveForSingleOffer($em, $offer)
    {
        if(!($offer->getIsCurrent())) {
            return;
        }
        
        $type = $offer->getType(); 
        if($type == Offer::OFFER_TYPE_CASHBACK || $type == Offer::OFFER_TYPE_SUBSCRIPTION_GAIN || $type == Offer::OFFER_TYPE_FULL_REIMBURSEMENT) {
            try{
                // To ensure that only 1 offer (the one being updated) is current 
                $query = $em->createQuery('UPDATE GD\AdminBundle\Entity\Offer o SET o.isCurrent = 0 WHERE o.type = :type AND o.merchant = :merchantId AND o.id <> :offerId');
                $query->setParameters(array('type' => $type, 'merchantId' => $offer->getMerchant()->getId(), 'offerId' => $offer->getId()));
                $query->execute();
                
               //Notifying the search index about the update   
                $oldOffer = $em->getRepository('GDAdminBundle:Offer')->findOneBy(array('merchant' => $offer->getMerchant()->getId(),'type' => $type,'isCurrent' => 0));
                $this->setLocale($offer);
                if($oldOffer){
                  $searchIndex = new SearchUtility();                
                  $searchIndex->deleteDocument($oldOffer, $this->locale);
                }
                
            } catch (\Exception $e) {
                $this->logger->err('****ERROR**** There was a problem in setting is_current to false for all offers of type:'.$offer->getTypeAsString().' .The error occured while saving Offer with ID: '. $offer->getId());
            }
        }
    }
    
    /**
     *  The post persist operation is invoked for all insert operations to the entities.This method  is used to 
     *  add some events while inserting into the User and and Offer entities.
     *  
     * @param LifecycleEventArgs $args 
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof User) {
            $this->addJoiningBonus($em, $entity);
            $this->addReferralBonus($em, $entity);
        }
        
        if ($entity instanceof Offer) {
            $this->setIsActiveForSingleOffer($em, $entity);
        }
    }

  /**
   * Sets the locale value to update the index
   * @param object $entity The entity object.
   */
   public function setLocale($entity) {
    $this->locale = $entity->getTranslatableLocale();
    $this->locale = (empty($this->locale)) ? $this->router->getContext()->getParameter('_locale') : $this->locale;
  }
}
