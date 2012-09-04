<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Feedback;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFeedbackData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        $fbk01 = new Feedback();
//        $fbk01->setIpAddress("143.131.22.1");
//        $fbk01->setRating(4);
//        $fbk01->setComment("This is awesome!");
//        $fbk01->setCreatedAt(new \DateTime('now'));
//        $fbk01->setUpdatedAt(new \DateTime('now'));
//        $fbk01->setUser(11);
//        $fbk01->setMerchantMasterId(1);
//        $manager->persist($fbk01);
//
//        $fbk02 = new Feedback();
//        $fbk02->setIpaddress("123.121.34.44");
//        $fbk02->setRating(2);
//        $fbk02->setComment("This is comment!");
//        $fbk02->setCrtTs(new \DateTime('now'));
//        $fbk02->setUpdTs(new \DateTime('now'));
//        $fbk02->setSiteUserId(21);
//        $fbk02->setMerchantMasterId(1);
//        $manager->persist($fbk02);
//
//        $fbk03 = new Feedback();
//        $fbk03->setIpaddress("344.443.55.555");
//        $fbk03->setRating(5);
//        $fbk03->setComment("This is incredible!");
//        $fbk03->setCrtTs(new \DateTime('now'));
//        $fbk03->setUpdTs(new \DateTime('now'));
//        $fbk03->setSiteUserId(23);
//        $fbk03->setMerchantMasterId(1);
//        $manager->persist($fbk03);
//
//        $fbk04 = new Feedback();
//        $fbk04->setIpaddress("323.563.33.511");
//        $fbk04->setRating(5);
//        $fbk04->setComment("This is incredible!");
//        $fbk04->setCrtTs(new \DateTime('now'));
//        $fbk04->setUpdTs(new \DateTime('now'));
//        $fbk04->setSiteUserId(1);
//        $fbk04->setMerchantMasterId(1);
//        $manager->persist($fbk04);
//
//        $fbk05 = new Feedback();
//        $fbk05->setIpaddress("323.333.334.591");
//        $fbk05->setRating(1);
//        $fbk05->setComment("Not good");
//        $fbk05->setCrtTs(new \DateTime('now'));
//        $fbk05->setUpdTs(new \DateTime('now'));
//        $fbk05->setSiteUserId(33);
//        $fbk05->setMerchantMasterId(1);
//        $manager->persist($fbk05);
//
//        $fbk06 = new Feedback();
//        $fbk06->setIpaddress("111.222.343.556");
//        $fbk06->setRating(1);
//        $fbk06->setComment("Not good");
//        $fbk06->setCrtTs(new \DateTime('now'));
//        $fbk06->setUpdTs(new \DateTime('now'));
//        $fbk06->setSiteUserId(12);
//        $fbk06->setMerchantMasterId(1);
//        $manager->persist($fbk06);
//
//        $fbk07 = new Feedback();
//        $fbk07->setIpaddress("133.244.344.522");
//        $fbk07->setRating(1);
//        $fbk07->setComment("Not good");
//        $fbk07->setCrtTs(new \DateTime('now'));
//        $fbk07->setUpdTs(new \DateTime('now'));
//        $fbk07->setSiteUserId(13);
//        $fbk07->setMerchantMasterId(1);
//        $manager->persist($fbk07);
//
//        $fbk08 = new Feedback();
//        $fbk08->setIpaddress("330.245.345.521");
//        $fbk08->setRating(1);
//        $fbk08->setComment("Not good");
//        $fbk08->setCrtTs(new \DateTime('now'));
//        $fbk08->setUpdTs(new \DateTime('now'));
//        $fbk08->setSiteUserId(10);
//        $fbk08->setMerchantMasterId(1);
//        $manager->persist($fbk08);
//
//        $fbk09 = new Feedback();
//        $fbk09->setIpaddress("111.112.113.114");
//        $fbk09->setRating(3);
//        $fbk09->setComment("Nice Offer");
//        $fbk09->setCrtTs(new \DateTime('now'));
//        $fbk09->setUpdTs(new \DateTime('now'));
//        $fbk09->setSiteUserId(15);
//        $fbk09->setMerchantMasterId(1);
//        $manager->persist($fbk09);
//
//        $fbk10 = new Feedback();
//        $fbk10->setIpaddress("101.102.103.104");
//        $fbk10->setRating(4);
//        $fbk10->setComment("Nice Offer adeswdswd");
//        $fbk10->setCrtTs(new \DateTime('now'));
//        $fbk10->setUpdTs(new \DateTime('now'));
//        $fbk10->setSiteUserId(16);
//        $fbk10->setMerchantMasterId(1);
//        $manager->persist($fbk10);

//        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
