<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Referral;
use Doctrine\Common\Persistence\ObjectManager;

class LoadReferralData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
//        $referral1 = new Referral();
//        $referral1->setReferralEmail('user1@mail.com');
//        $referral1->setUser($manager->merge($this->getReference('user-01')));
//        //$referral1->setTransaction($this->getReference('transaction-09'));
//        $referral1->setCreatedAt(new \DateTime());
//        $manager->persist($referral1);
//
//        $referral2 = new Referral();
//        $referral2->setReferralEmail('user2@mail.com');
//        $referral2->setUser($manager->merge($this->getReference('user-01')));
//        //$referral2->setTransaction($this->getReference('transaction-10'));
//        $referral2->setCreatedAt(new \DateTime());
//        $manager->persist($referral2);
//
//        $referral3 = new Referral();
//        $referral3->setReferralEmail('user3@mail.com');
//        $referral3->setUser($manager->merge($this->getReference('user-01')));
//        //$referral3->setTransaction($this->getReference('transaction-11'));
//        $referral3->setCreatedAt(new \DateTime());
//        $manager->persist($referral3);
//
//        $referral4 = new Referral();
//        $referral4->setReferralEmail('user4@mail.com');
//        $referral4->setUser($manager->merge($this->getReference('user-01')));
//        //$referral4->setTransaction($this->getReference('transaction-12'));
//        $referral4->setCreatedAt(new \DateTime());
//        $manager->persist($referral4);
//
//        $referral5 = new Referral();
//        $referral5->setReferralEmail('user5@mail.com');
//        $referral5->setUser($manager->merge($this->getReference('user-02')));
//        //$referral5->setTransaction($this->getReference('transaction-13'));
//        $referral5->setCreatedAt(new \DateTime());
//        $manager->persist($referral5);
//
//        $referral6 = new Referral();
//        $referral6->setReferralEmail('user6@mail.com');
//        //$referral6->setTransaction($this->getReference('transaction-14'));
//        $referral6->setUser($manager->merge($this->getReference('user-02')));
//        $referral6->setCreatedAt(new \DateTime());
//        $manager->persist($referral6);
//
//        $manager->flush();
//
//        $this->addReference('referral-01', $referral1);
//        $this->addReference('referral-02', $referral2);
//        $this->addReference('referral-03', $referral3);
//        $this->addReference('referral-04', $referral4);
//        $this->addReference('referral-05', $referral5);
//        $this->addReference('referral-06', $referral6);

    }

    public function getOrder()
    {
        return 15;
    }


}
