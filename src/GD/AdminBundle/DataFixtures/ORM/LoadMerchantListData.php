<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\MerchantList;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMerchantListData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        $newList = new MerchantList();
//        $newList->setName("new-merchants");
//
//        $manager->persist($newList);
//
//        $topList = new MerchantList();
//        $topList->setName("top-merchants");
//
//        $manager->persist($topList);
//
//        $privateList = new MerchantList();
//        $privateList->setName("private-merchants");
//
//        $manager->persist($privateList);
//
//        $cashbackMerchants = new MerchantList();
//        $cashbackMerchants->setType(MerchantList::LIST_TYPE_CAROUSEL);
//        $cashbackMerchants->setName("cashback-merchants");
//        $manager->persist($cashbackMerchants);
//
//        $codepromoMerchants = new MerchantList();
//        $codepromoMerchants->setType(MerchantList::LIST_TYPE_CAROUSEL);
//        $codepromoMerchants->setName("codepromo-merchants");
//        $manager->persist($codepromoMerchants);
//
//        $manager->flush();
    }

    public function getOrder()
    {
        return 12;
    }
}
