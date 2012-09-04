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
        $newList = new MerchantList();
        $newList->setName("new-merchants");
//        $newList->addMerchant($this->getReference('merc-01'));
//        $newList->addMerchant($this->getReference('merc-03'));
//        $newList->addMerchant($this->getReference('merc-05'));
        $manager->persist($newList);

        $topList = new MerchantList();
        $topList->setName("top-merchants");
//        $topList->addMerchant($this->getReference('merc-01'));
//        $topList->addMerchant($this->getReference('merc-06'));
//        $topList->addMerchant($this->getReference('merc-04'));
        $manager->persist($topList);

        $privateList = new MerchantList();
        $privateList->setName("private-merchants");
//        $privateList->addMerchant($this->getReference('merc-10'));
//        $privateList->addMerchant($this->getReference('merc-05'));
//        $privateList->addMerchant($this->getReference('merc-08'));
        $manager->persist($privateList);

        $cashbackMerchants = new MerchantList();
        $cashbackMerchants->setType(MerchantList::LIST_TYPE_CAROUSEL);
        $cashbackMerchants->setName("cashback-merchants");
        $manager->persist($cashbackMerchants);

        $codepromoMerchants = new MerchantList();
        $codepromoMerchants->setType(MerchantList::LIST_TYPE_CAROUSEL);
        $codepromoMerchants->setName("codepromo-merchants");
        $manager->persist($codepromoMerchants);

        $manager->flush();
    }

    public function getOrder()
    {
        return 12;
    }
}
