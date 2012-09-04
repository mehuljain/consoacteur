<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Request;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRequestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        $request01 = new Request();
//        $request01->setUser($this->getReference('user-01'));
//        $request01->setProblemId(1);
//        $request01->setRequestDate(new \DateTime());
//        $request01->setSubject("Test Subject");
//        $request01->setMessage("Request message body goes in here!");
//        $request01->setRequestNumber(uniqid());
//        $manager->persist($request01);
//
//        $request02 = new Request();
//        $request02->setUser($this->getReference('user-01'));
//        $request02->setProblemId(2);
//        $request02->setRequestDate(new \DateTime());
//        $request02->setSubject("Test Subject test");
//        $request02->setMessage("Request message body goes in here! This is not good");
//        $request02->setRequestNumber(uniqid());
//        $manager->persist($request02);
//
//        $request03 = new Request();
//        $request03->setUser($this->getReference('user-02'));
//        $request03->setProblemId(3);
//        $request03->setRequestDate(new \DateTime());
//        $request03->setSubject("Test Subject test adecfacfaca");
//        $request03->setMessage("efefwef afaf Request message body goes in here! This is not good aefafa");
//        $request03->setRequestNumber(uniqid());
//        $manager->persist($request03);
//
//        $request04 = new Request();
//        $request04->setUser($this->getReference('user-02'));
//        $request04->setProblemId(4);
//        $request04->setRequestDate(new \DateTime());
//        $request04->setSubject("Test Subject test 2 adecfacfaca");
//        $request04->setMessage("Request message body goes in here! This is test message");
//        $request04->setRequestNumber(uniqid());
//        $manager->persist($request04);
//
//        $manager->flush();

    }

    public function getOrder()
    {
        return 8;
    }
}
