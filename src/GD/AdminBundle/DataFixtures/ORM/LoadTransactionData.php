<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Transaction;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTransactionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //TRANSACTION_STATUS_WAITING
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans1 = new Transaction();
//        $trans1->setUser($this->getReference('user-01'));
//        $trans1->setType(Transaction::TRANSACTION_TYPE_OFFER);
//        $trans1->setOffer($this->getReference('offer-01'));
//        $trans1->setStatus(Transaction::TRANSACTION_STATUS_WAITING);
//        $trans1->setUserGainAmount(25);
//        $trans1->setCommissionAmount(12);
//        $trans1->setTransactionAmount(500);
//        $trans1->setCreatedAt($createdDate);
//        $trans1->setValidationDate(new \DateTime());
//        $manager->persist($trans1);
//        $manager->flush();
//        
//        //TRANSACTION_STATUS_WAITING
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-15 days');
//        $trans2 = new Transaction();
//        $trans2->setUser($this->getReference('user-01'));
//        $trans2->setType(Transaction::TRANSACTION_TYPE_OFFER);
//        $trans2->setOffer($this->getReference('offer-02'));
//        $trans2->setStatus(Transaction::TRANSACTION_STATUS_WAITING);
//        $trans2->setUserGainAmount(5);
//        $trans2->setCommissionAmount(2);
//        $trans2->setTransactionAmount(50);
//        $trans2->setCreatedAt($createdDate);
//        $trans2->setValidationDate(new \DateTime());
//        $manager->persist($trans2);
//        $manager->flush();
//
//        //TRANSACTION_STATUS_CONFIRMED
//        $trans3 = new Transaction();
//        $trans3->setUser($this->getReference('user-01'));
//        $trans3->setType(Transaction::TRANSACTION_TYPE_OFFER);
//        $trans3->setOffer($this->getReference('offer-03'));
//        $trans3->setStatus(Transaction::TRANSACTION_STATUS_CONFIRMED);
//        $trans3->setUserGainAmount(100);
//        $trans3->setCommissionAmount(20);
//        $trans3->setTransactionAmount(1100);
//        $trans3->setCreatedAt(new \DateTime());
//        $trans3->setValidationDate(new \DateTime());
//        $manager->persist($trans3);
//        $manager->flush();
//        
//        //No fixture for TRANSACTION_STATUS_PAYMENT_REQUESTED
//        
//        //TRANSACTION_STATUS_PAID
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-30 days');
//        $trans4 = new Transaction();
//        $trans4->setUser($this->getReference('user-01'));
//        $trans4->setType(Transaction::TRANSACTION_TYPE_OFFER);
//        $trans4->setOffer($this->getReference('fr-offer-11'));
//        $trans4->setStatus(Transaction::TRANSACTION_STATUS_PAID);
//        $trans4->setUserGainAmount(15);
//        $trans4->setCommissionAmount(6);
//        $trans4->setTransactionAmount(150);
//        $trans4->setCreatedAt($createdDate);
//        $trans4->setValidationDate(new \DateTime());
//        $manager->persist($trans4);
//        $manager->flush();
//
//        //TRANSACTION_STATUS_CANCELLED
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-50 days');
//        $trans5 = new Transaction();
//        $trans5->setUser($this->getReference('user-01'));
//        $trans5->setType(Transaction::TRANSACTION_TYPE_OFFER);
//        $trans5->setOffer($this->getReference('fr-offer-12'));
//        $trans5->setStatus(Transaction::TRANSACTION_STATUS_CANCELLED);
//        $trans5->setUserGainAmount(25);
//        $trans5->setCommissionAmount(15);
//        $trans5->setTransactionAmount(350);
//        $trans5->setCreatedAt($createdDate);
//        $trans5->setValidationDate(new \DateTime());
//        $manager->persist($trans5);
//        $manager->flush();
//
//        //TRANSACTION_STATUS_REJECTED
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-60 days');
//        $trans6 = new Transaction();
//        $trans6->setUser($this->getReference('user-01'));
//        $trans6->setType(Transaction::TRANSACTION_TYPE_OFFER);
//        $trans6->setOffer($this->getReference('sg-offer-13'));
//        $trans6->setStatus(Transaction::TRANSACTION_STATUS_REJECTED);
//        $trans6->setUserGainAmount(5);
//        $trans6->setCommissionAmount(2);
//        $trans6->setCreatedAt($createdDate);
//        $trans6->setValidationDate(new \DateTime());
//        $manager->persist($trans6);
//        $manager->flush();
//
//        //TRANSACTION_STATUS_ON_HOLD
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans7 = new Transaction();
//        $trans7->setUser($this->getReference('user-01'));
//        $trans7->setType(Transaction::TRANSACTION_TYPE_OFFER);
//        $trans7->setOffer($this->getReference('sg-offer-14'));
//        $trans7->setStatus(Transaction::TRANSACTION_STATUS_ON_HOLD);
//        $trans7->setUserGainAmount(10);
//        $trans7->setCommissionAmount(4);
//        $trans7->setCreatedAt($createdDate);
//        $trans7->setValidationDate(new \DateTime());
//        $trans7->setWithdrawal($this->getReference('withdrawal-20'));
//        $manager->persist($trans7);
//        $manager->flush();
//
//        //TRANSACTION_STATUS_LOST
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans8 = new Transaction();
//        $trans8->setUser($this->getReference('user-01'));
//        $trans8->setType(Transaction::TRANSACTION_TYPE_OFFER);
//        $trans8->setOffer($this->getReference('fr-offer-26'));
//        $trans8->setStatus(Transaction::TRANSACTION_STATUS_LOST);
//        $trans8->setUserGainAmount(10);
//        $trans8->setCommissionAmount(4);
//        $trans8->setCreatedAt($createdDate);
//        $trans8->setValidationDate(new \DateTime());
//        $manager->persist($trans8);
//        $manager->flush();
//        
//        //TRANSACTION_TYPE_REFERRAL TRANSACTION_STATUS_PENDING_CONFIRMATION
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans9 = new Transaction();
//        $trans9->setUser($this->getReference('user-01'));
//        $trans9->setType(Transaction::TRANSACTION_TYPE_REFERRAL);
//        $trans9->setStatus(Transaction::TRANSACTION_STATUS_PENDING_CONFIRMATION);
//        $trans9->setUserGainAmount(10);
//        $trans9->setCommissionAmount(4);
//        $trans9->setCreatedAt($createdDate);
//        $trans9->setValidationDate(new \DateTime());
//        $manager->persist($trans9);
//        $manager->flush();
//        
//        //TRANSACTION_TYPE_REFERRAL TRANSACTION_STATUS_PAID
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans10 = new Transaction();
//        $trans10->setUser($this->getReference('user-02'));
//        $trans10->setType(Transaction::TRANSACTION_TYPE_REFERRAL);
//        $trans10->setStatus(Transaction::TRANSACTION_STATUS_PAID);
//        $trans10->setUserGainAmount(10);
//        $trans10->setCommissionAmount(4);
//        $trans10->setCreatedAt($createdDate);
//        $trans10->setValidationDate(new \DateTime());
//        $manager->persist($trans10);
//        $manager->flush();
//
//        //TRANSACTION_TYPE_REFERRAL TRANSACTION_STATUS_PENDING_CONFIRMATION
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans11 = new Transaction();
//        $trans11->setUser($this->getReference('user-01'));
//        $trans11->setType(Transaction::TRANSACTION_TYPE_REFERRAL);
//        $trans11->setStatus(Transaction::TRANSACTION_STATUS_PAID);
//        $trans11->setUserGainAmount(10);
//        $trans11->setCommissionAmount(4);
//        $trans11->setCreatedAt($createdDate);
//        $trans11->setValidationDate(new \DateTime());
//        $manager->persist($trans9);
//        $manager->flush();
//
//        //TRANSACTION_TYPE_REFERRAL TRANSACTION_STATUS_CONFIRMED
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans12 = new Transaction();
//        $trans12->setUser($this->getReference('user-02'));
//        $trans12->setType(Transaction::TRANSACTION_TYPE_REFERRAL);
//        $trans12->setStatus(Transaction::TRANSACTION_STATUS_CONFIRMED);
//        $trans12->setUserGainAmount(10);
//        $trans12->setCommissionAmount(4);
//        $trans12->setCreatedAt($createdDate);
//        $trans12->setValidationDate(new \DateTime());
//        $manager->persist($trans12);
//        $manager->flush();
//
//        //TRANSACTION_TYPE_REFERRAL TRANSACTION_STATUS_CONFIRMED
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans13 = new Transaction();
//        $trans13->setUser($this->getReference('user-01'));
//        $trans13->setType(Transaction::TRANSACTION_TYPE_REFERRAL);
//        $trans13->setStatus(Transaction::TRANSACTION_STATUS_CONFIRMED);
//        $trans13->setUserGainAmount(10);
//        $trans13->setCommissionAmount(4);
//        $trans13->setCreatedAt($createdDate);
//        $trans13->setValidationDate(new \DateTime());
//        $manager->persist($trans13);
//        $manager->flush();
//        
//        //TRANSACTION_TYPE_REFERRAL TRANSACTION_STATUS_CONFIRMED
//        $createdDate = new \DateTime(); 
//        $createdDate->modify('-10 days');
//        $trans14 = new Transaction();
//        $trans14->setUser($this->getReference('user-02'));
//        $trans14->setType(Transaction::TRANSACTION_TYPE_REFERRAL);
//        $trans14->setStatus(Transaction::TRANSACTION_STATUS_CONFIRMED);
//        $trans14->setUserGainAmount(10);
//        $trans14->setCommissionAmount(4);
//        $trans14->setCreatedAt($createdDate);
//        $trans14->setValidationDate(new \DateTime());
//        $manager->persist($trans14);
//        $manager->flush();
//
//
//        $this->addReference('transaction-09', $trans9);
//        $this->addReference('transaction-10', $trans10);
//        $this->addReference('transaction-11', $trans11);
//        $this->addReference('transaction-12', $trans12);
//        $this->addReference('transaction-13', $trans13);
//        $this->addReference('transaction-14', $trans14);
        
    }

    public function getOrder()
    {
        return 17;
    }


}
