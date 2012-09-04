<?php

namespace GD\SiteBundle\Listener;

use GD\AdminBundle\Entity\Offer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Monolog\Logger;

class preUpdateListener
{
    protected $logger;
    
    /**
     * The constructor method
     * 
     * @param Logger $logger 
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * In case the backend user creates multiple cashback, full reimbursement or subscription gain offer for a single merchant,
     * this function ensures that the corresponding old offer for this merchant is not currrent. 
     * 
     * @param EntityManager $em The entity manager object
     * @param Offer $offer The offer entity object
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
            } catch (\Exception $e) {
                $this->logger->err('****ERROR**** There was a problem in setting is_current to false for all offers of type:'.$offer->getTypeAsString().' .The error occured while saving Offer with ID: '. $offer->getId());
            }
        }
    }

    /**
     *  This Doctrine event is invoked before all the update operations made to the Doctrine entity.
     * @param LifecycleEventArgs $args 
     */
    
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Offer) {
            $this->setIsActiveForSingleOffer($em, $entity);
        }
    }
}
