<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Withdrawal;
use Doctrine\Common\Persistence\ObjectManager;

class LoadWithdrawalData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Confirmed and Validated Amount
//        $withdrawal1 = new Withdrawal();
//        $withdrawal1->setCreatedAt(new \DateTime());
//        $withdrawal1->setCode(111435);
//        $withdrawal1->setAmount(24);
//        $withdrawal1->setType(1);
//        $withdrawal1->setStatus(3);
//        $withdrawal1->setValidatedAt(new \DateTime());
//        $withdrawal1->setUser($manager->merge($this->getReference('user-01')));
//        $manager->persist($withdrawal1);
//
//        $withdrawal2 = new Withdrawal();
//        $withdrawal2->setCreatedAt(new \DateTime());
//        $withdrawal2->setCode(1112435);
//        $withdrawal2->setAmount(45);
//        $withdrawal2->setType(1);
//        $withdrawal2->setStatus(4);
//        $withdrawal2->setValidatedAt(new \DateTime());
//        $withdrawal2->setUser($manager->merge($this->getReference('user-01')) );
//        $withdrawal2->setReferrals($manager->merge($this->getReference('referral-02')));
//        $manager->persist($withdrawal2);
//
//        $withdrawal3 = new Withdrawal();
//        $withdrawal3->setCreatedAt(new \DateTime());
//        $withdrawal3->setCode(1435);
//        $withdrawal3->setAmount(43);
//        $withdrawal3->setType(1);
//        $withdrawal3->setStatus(5);
//        $withdrawal3->setValidatedAt(new \DateTime());
//        $withdrawal3->setUser($manager->merge($this->getReference('user-01')) );
//        $withdrawal3->setReferrals($manager->merge($this->getReference('referral-03')));
//        $manager->persist($withdrawal3);
//
//        $withdrawal4 = new Withdrawal();
//        $withdrawal4->setCreatedAt(new \DateTime());
//        $withdrawal4->setCode(231435);
//        $withdrawal4->setAmount(54);
//        $withdrawal4->setType(1);
//        $withdrawal4->setStatus(3);
//        $withdrawal4->setValidatedAt(new \DateTime());
//        $withdrawal4->setUser($manager->merge($this->getReference('user-02')) );
//        $withdrawal4->setReferrals($manager->merge($this->getReference('referral-04')));
//        $manager->persist($withdrawal4);
//
//        $withdrawal5 = new Withdrawal();
//        $withdrawal5->setCreatedAt(new \DateTime());
//        $withdrawal5->setCode(1114235);
//        $withdrawal5->setAmount(54);
//        $withdrawal5->setType(1);
//        $withdrawal5->setStatus(4);
//        $withdrawal5->setValidatedAt(new \DateTime());
//        $withdrawal5->setUser($manager->merge($this->getReference('user-02')) );
//        $manager->persist($withdrawal5);
//
//        $withdrawal6 = new Withdrawal();
//        $withdrawal6->setCreatedAt(new \DateTime());
//        $withdrawal6->setCode(111435);
//        $withdrawal6->setAmount(54);
//        $withdrawal6->setStatus(5);
//        $withdrawal6->setType(1);
//        $withdrawal6->setValidatedAt(new \DateTime());
//        $withdrawal6->setUser($manager->merge($this->getReference('user-02')) );
//        $manager->persist($withdrawal6);
//
//        // Cashback Amount to be validated
//        $withdrawal7 = new Withdrawal();
//        $withdrawal7->setCreatedAt(new \DateTime());
//        $withdrawal7->setCode(111435);
//        $withdrawal7->setAmount(87);
//        $withdrawal7->setStatus(1);
//        $withdrawal7->setType(1);
//        $withdrawal7->setValidatedAt(new \DateTime());
//        $withdrawal7->setUser($manager->merge($this->getReference('user-01')) );
//        $manager->persist($withdrawal7);
//
//        $withdrawal8 = new Withdrawal();
//        $withdrawal8->setCreatedAt(new \DateTime());
//        $withdrawal8->setCode(111435);
//        $withdrawal8->setAmount(54);
//        $withdrawal8->setStatus(2);
//        $withdrawal8->setType(1);
//        $withdrawal8->setValidatedAt(new \DateTime());
//        $withdrawal8->setUser($manager->merge($this->getReference('user-01')) );
//        $manager->persist($withdrawal8);
//
//        $withdrawal9 = new Withdrawal();
//        $withdrawal9->setCreatedAt(new \DateTime());
//        $withdrawal9->setCode(111435);
//        $withdrawal9->setAmount(54);
//        $withdrawal9->setStatus(1);
//        $withdrawal9->setType(1);
//        $withdrawal9->setValidatedAt(new \DateTime());
//        $withdrawal9->setUser($manager->merge($this->getReference('user-02')) );
//        $manager->persist($withdrawal9);
//
//        $withdrawal10 = new Withdrawal();
//        $withdrawal10->setCreatedAt(new \DateTime());
//        $withdrawal10->setCode(111435);
//        $withdrawal10->setAmount(54);
//        $withdrawal10->setStatus(2);
//        $withdrawal10->setType(1);
//        $withdrawal10->setValidatedAt(new \DateTime());
//        $withdrawal10->setUser($manager->merge($this->getReference('user-02')) );
//        $manager->persist($withdrawal10);
//
//        // Referral Amount to be validated
//        $withdrawal11 = new Withdrawal();
//        $withdrawal11->setCreatedAt(new \DateTime());
//        $withdrawal11->setCode(111435);
//        $withdrawal11->setAmount(54);
//        $withdrawal11->setStatus(7);
//        $withdrawal11->setType(1);
//        $withdrawal11->setValidatedAt(new \DateTime());
//        $withdrawal11->setUser($manager->merge($this->getReference('user-01')) );
//        $withdrawal11->setReferrals($manager->merge($this->getReference('referral-01')));
//        $manager->persist($withdrawal11);
//
//        $withdrawal12 = new Withdrawal();
//        $withdrawal12->setCreatedAt(new \DateTime());
//        $withdrawal12->setCode(111435);
//        $withdrawal12->setAmount(54);
//        $withdrawal12->setStatus(7);
//        $withdrawal12->setType(1);
//        $withdrawal12->setValidatedAt(new \DateTime());
//        $withdrawal12->setUser($manager->merge($this->getReference('user-01')) );
//        $withdrawal12->setReferrals($manager->merge($this->getReference('referral-02')));
//        $manager->persist($withdrawal12);
//
//        $withdrawal13 = new Withdrawal();
//        $withdrawal13->setCreatedAt(new \DateTime());
//        $withdrawal13->setCode(4535);
//        $withdrawal13->setAmount(2);
//        $withdrawal13->setStatus(2);
//        $withdrawal13->setType(1);
//        $withdrawal13->setValidatedAt(new \DateTime());
//        $withdrawal13->setUser($manager->merge($this->getReference('user-02')) );
//        $withdrawal13->setReferrals($manager->merge($this->getReference('referral-03')));
//        $manager->persist($withdrawal13);
//
//        $withdrawal14 = new Withdrawal();
//        $withdrawal14->setCreatedAt(new \DateTime());
//        $withdrawal14->setCode(4535);
//        $withdrawal14->setAmount(2);
//        $withdrawal14->setStatus(3);
//        $withdrawal14->setType(1);
//        $withdrawal14->setValidatedAt(new \DateTime());
//        $withdrawal14->setUser($manager->merge($this->getReference('user-02')) );
//        $withdrawal14->setReferrals($manager->merge($this->getReference('referral-04')));
//        $manager->persist($withdrawal14);
//
//        // Cancelled Cashback Amount
//        $withdrawal15 = new Withdrawal();
//        $withdrawal15->setCreatedAt(new \DateTime());
//        $withdrawal15->setCode(4535);
//        $withdrawal15->setAmount(21);
//        $withdrawal15->setStatus(6);
//        $withdrawal15->setType(1);
//        $withdrawal15->setValidatedAt(new \DateTime());
//        $withdrawal15->setUser($manager->merge($this->getReference('user-01')) );
//        $manager->persist($withdrawal15);
//
//        $withdrawal16 = new Withdrawal();
//        $withdrawal16->setCreatedAt(new \DateTime());
//        $withdrawal16->setCode(4535);
//        $withdrawal16->setAmount(21);
//        $withdrawal16->setStatus(6);
//        $withdrawal16->setType(1);
//        $withdrawal16->setValidatedAt(new \DateTime());
//        $withdrawal16->setUser($manager->merge($this->getReference('user-01')) );
//        $manager->persist($withdrawal16);
//
//        $withdrawal17 = new Withdrawal();
//        $withdrawal17->setCreatedAt(new \DateTime());
//        $withdrawal17->setCode(4535);
//        $withdrawal17->setAmount(21);
//        $withdrawal17->setStatus(6);
//        $withdrawal17->setType(1);
//        $withdrawal17->setValidatedAt(new \DateTime());
//        $withdrawal17->setUser($manager->merge($this->getReference('user-02')) );
//        $manager->persist($withdrawal17);
//
//        $withdrawal18 = new Withdrawal();
//        $withdrawal18->setCreatedAt(new \DateTime());
//        $withdrawal18->setCode(4535);
//        $withdrawal18->setAmount(21);
//        $withdrawal18->setStatus(6);
//        $withdrawal18->setType(1);
//        $withdrawal18->setValidatedAt(new \DateTime());
//        $withdrawal18->setUser($manager->merge($this->getReference('user-02')) );
//        $manager->persist($withdrawal18);
//
//        // Rejected Cashback Amount
//        $withdrawal19 = new Withdrawal();
//        $withdrawal19->setCreatedAt(new \DateTime());
//        $withdrawal19->setCode(4535);
//        $withdrawal19->setAmount(21);
//        $withdrawal19->setStatus(7);
//        $withdrawal19->setType(1);
//        $withdrawal19->setValidatedAt(new \DateTime());
//        $withdrawal19->setUser($manager->merge($this->getReference('user-01')) );
//        $manager->persist($withdrawal19);
//
//        $withdrawal20 = new Withdrawal();
//        $withdrawal20->setCreatedAt(new \DateTime());
//        $withdrawal20->setCode(4535);
//        $withdrawal20->setAmount(21);
//        $withdrawal20->setStatus(7);
//        $withdrawal20->setType(1);
//        $withdrawal20->setUserComment('Put on hold because of some reason');
//        $withdrawal20->setValidatedAt(new \DateTime());
//        $withdrawal20->setUser($manager->merge($this->getReference('user-01')) );
//        $manager->persist($withdrawal20);
//
//        $withdrawal21 = new Withdrawal();
//        $withdrawal21->setCreatedAt(new \DateTime());
//        $withdrawal21->setCode(4535);
//        $withdrawal21->setAmount(21);
//        $withdrawal21->setStatus(7);
//        $withdrawal21->setType(1);
//        $withdrawal21->setValidatedAt(new \DateTime());
//        $withdrawal21->setUser($manager->merge($this->getReference('user-02')) );
//        $manager->persist($withdrawal21);
//
//        $withdrawal22 = new Withdrawal();
//        $withdrawal22->setCreatedAt(new \DateTime());
//        $withdrawal22->setCode(4535);
//        $withdrawal22->setAmount(21);
//        $withdrawal22->setStatus(7);
//        $withdrawal22->setType(1);
//        $withdrawal22->setValidatedAt(new \DateTime());
//        $withdrawal22->setUser($manager->merge($this->getReference('user-02')) );
//        $manager->persist($withdrawal22);
//
//
//        $manager->flush();
//        
//        $this->addReference('withdrawal-20', $withdrawal20);
    }

    public function getOrder()
    {
        return 16;
    }


}
