<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Merchant;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMerchantData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        $merc01 = new Merchant();
//        $merc01->setName("őMerchant01");
//        $merc01->setTitle("SEOTitle");
//        $merc01->setDescription("This is the Description of Merchant 01");
//        $merc01->setImage("/bundles/gdsite/images/m1.jpg");
//        $merc01->setDefaultAverageFeedback(3);
//        $merc01->setIsActive(true);
//        $merc01->setCreatedAt(new \DateTime());
//        $merc01->setUpdatedAt(new \DateTime());
//        $merc01->setPrimaryCategory($this->getReference('cat-01'));
//        $merc01->setPrimaryTag($this->getReference('tag-01'));
//        $merc01->setTranslatableLocale("en");
//        $merc01->addTag($this->getReference('tag-01'));
//        $merc01->addTag($this->getReference('tag-02'));
//        $merc01->addTag($this->getReference('tag-03'));
//        $merc01->addTag($this->getReference('tag-04'));
//        $merc01->addCategory($this->getReference('cat-01'));
//        $merc01->addCategory($this->getReference('cat-02'));
//        $merc01->setAffiliatePartner($this->getReference('affiliatePartner-01'));
//        $manager->persist($merc01);
//
//        $merc02 = new Merchant();
//        $merc02->setName("AMerchant02");
//        $merc02->setTitle("SEOTitle");
//        $merc02->setDescription("This is the Description of Merchant 02");
//        $merc02->setImage("/bundles/gdsite/images/m2.jpg");
//        $merc02->setDefaultAverageFeedback(3);
//        $merc02->setIsActive(true);
//        $merc02->setCreatedAt(new \DateTime());
//        $merc02->setUpdatedAt(new \DateTime());
//        $merc02->setPrimaryCategory($this->getReference('cat-02'));
//        $merc02->setPrimaryTag($this->getReference('tag-03'));
//        $merc02->setTranslatableLocale("en");
//        $merc02->addTag($this->getReference('tag-02'));
//        $merc02->addTag($this->getReference('tag-03'));
//        $merc02->addTag($this->getReference('tag-04'));
//        $merc02->addTag($this->getReference('tag-05'));
//        $merc02->addCategory($this->getReference('cat-02'));
//        $merc02->addCategory($this->getReference('cat-03'));
//        $merc02->setAffiliatePartner($this->getReference('affiliatePartner-01'));
//        $manager->persist($merc02);
//
//        $merc03 = new Merchant();
//        $merc03->setName("ßMerchant03");
//        $merc03->setTitle("SEOTitle");
//        $merc03->setDescription("This is the Description of Merchant 03");
//        $merc03->setImage("/bundles/gdsite/images/m3.jpg");
//        $merc03->setDefaultAverageFeedback(3);
//        $merc03->setIsActive(true);
//        $merc03->setCreatedAt(new \DateTime());
//        $merc03->setUpdatedAt(new \DateTime());
//        $merc03->setPrimaryCategory($this->getReference('cat-03'));
//        $merc03->setPrimaryTag($this->getReference('tag-05'));
//        $merc03->setTranslatableLocale("en");
//        $merc03->addTag($this->getReference('tag-03'));
//        $merc03->addTag($this->getReference('tag-04'));
//        $merc03->addTag($this->getReference('tag-05'));
//        $merc03->addTag($this->getReference('tag-06'));
//        $merc03->addCategory($this->getReference('cat-03'));
//        $merc03->addCategory($this->getReference('cat-04'));
//        $merc03->setAffiliatePartner($this->getReference('affiliatePartner-02'));
//        $manager->persist($merc03);
//
//        $merc04 = new Merchant();
//        $merc04->setName("øMerchant04");
//        $merc04->setTitle("SEOTitle");
//        $merc04->setDescription("This is the Description of Merchant 04");
//        $merc04->setImage("/bundles/gdsite/images/m4.jpg");
//        $merc04->setDefaultAverageFeedback(3);
//        $merc04->setIsActive(true);
//        $merc04->setCreatedAt(new \DateTime());
//        $merc04->setUpdatedAt(new \DateTime());
//        $merc04->setPrimaryCategory($this->getReference('cat-04'));
//        $merc04->setPrimaryTag($this->getReference('tag-07'));
//        $merc04->setTranslatableLocale("en");
//        $merc04->setAffiliatePartner($this->getReference('affiliatePartner-02'));
//        $manager->persist($merc04);
//
//        $merc05 = new Merchant();
//        $merc05->setName("űMerchant05");
//        $merc05->setTitle("SEOTitle");
//        $merc05->setDescription("This is the Description of Merchant 05");
//        $merc05->setImage("/bundles/gdsite/images/m5.jpg");
//        $merc05->setDefaultAverageFeedback(3);
//        $merc05->setIsActive(true);
//        $merc05->setCreatedAt(new \DateTime());
//        $merc05->setUpdatedAt(new \DateTime());
//        $merc05->setPrimaryCategory($this->getReference('cat-05'));
//        $merc05->setPrimaryTag($this->getReference('tag-09'));
//        $merc05->setTranslatableLocale("en");
//        $merc05->setAffiliatePartner($this->getReference('affiliatePartner-01'));
//        $manager->persist($merc05);
//
//        $merc06 = new Merchant();
//        $merc06->setName("ąMerchant06");
//        $merc06->setTitle("SEOTitle");
//        $merc06->setDescription("This is the Description of Merchant 06");
//        $merc06->setImage("/bundles/gdsite/images/m6.jpg");
//        $merc06->setDefaultAverageFeedback(3);
//        $merc06->setIsActive(true);
//        $merc06->setCreatedAt(new \DateTime());
//        $merc06->setUpdatedAt(new \DateTime());
//        $merc06->setPrimaryCategory($this->getReference('cat-01'));
//        $merc06->setPrimaryTag($this->getReference('tag-01'));
//        $merc06->setTranslatableLocale("en");
//        $merc06->setAffiliatePartner($this->getReference('affiliatePartner-01'));
//        $manager->persist($merc06);
//
//        $merc07 = new Merchant();
//        $merc07->setName("æMerchant07");
//        $merc07->setTitle("SEOTitle");
//        $merc07->setDescription("This is the Description of Merchant 07");
//        $merc07->setImage("/bundles/gdsite/images/m6.jpg");
//        $merc07->setDefaultAverageFeedback(3);
//        $merc07->setIsActive(true);
//        $merc07->setCreatedAt(new \DateTime());
//        $merc07->setUpdatedAt(new \DateTime());
//        $merc07->setPrimaryCategory($this->getReference('cat-01'));
//        $merc07->setPrimaryTag($this->getReference('tag-01'));
//        $merc07->setTranslatableLocale("en");
//        $merc07->setAffiliatePartner($this->getReference('affiliatePartner-02'));
//        $manager->persist($merc07);
//
//        $merc08 = new Merchant();
//        $merc08->setName("őMerchant08");
//        $merc08->setTitle("SEOTitle");
//        $merc08->setDescription("This is the Description of Merchant 08");
//        $merc08->setImage("/bundles/gdsite/images/m6.jpg");
//        $merc08->setDefaultAverageFeedback(3);
//        $merc08->setIsActive(true);
//        $merc08->setCreatedAt(new \DateTime());
//        $merc08->setUpdatedAt(new \DateTime());
//        $merc08->setPrimaryCategory($this->getReference('cat-02'));
//        $merc08->setPrimaryTag($this->getReference('tag-03'));
//        $merc08->setTranslatableLocale("en");
//        $merc08->setAffiliatePartner($this->getReference('affiliatePartner-01'));
//        $manager->persist($merc08);
//
//        $merc09 = new Merchant();
//        $merc09->setName("1Merchant09");
//        $merc09->setTitle("SEOTitle");
//        $merc09->setDescription("This is the Description of Merchant 09");
//        $merc09->setImage("/bundles/gdsite/images/m6.jpg");
//        $merc09->setDefaultAverageFeedback(4);
//        $merc09->setIsActive(true);
//        $merc09->setCreatedAt(new \DateTime());
//        $merc09->setUpdatedAt(new \DateTime());
//        $merc09->setPrimaryCategory($this->getReference('cat-05'));
//        $merc09->setPrimaryTag($this->getReference('tag-05'));
//        $merc09->setTranslatableLocale("en");
//        $merc09->setAffiliatePartner($this->getReference('affiliatePartner-02'));
//        $manager->persist($merc09);
//
//        $merc10 = new Merchant();
//        $merc10->setName("0Merchant10");
//        $merc10->setTitle("SEOTitle");
//        $merc10->setDescription("This is the Description of Merchant 10");
//        $merc10->setImage("/bundles/gdsite/images/m6.jpg");
//        $merc10->setDefaultAverageFeedback(1);
//        $merc10->setIsActive(true);
//        $merc10->setCreatedAt(new \DateTime());
//        $merc10->setUpdatedAt(new \DateTime());
//        $merc10->setPrimaryCategory($this->getReference('cat-05'));
//        $merc10->setPrimaryTag($this->getReference('tag-05'));
//        $merc10->setTranslatableLocale("en");
//        $merc10->setAffiliatePartner($this->getReference('affiliatePartner-01'));
//        $manager->persist($merc10);
//
//        $manager->flush();
//
//        $this->addReference('merc-01', $merc01);
//        $this->addReference('merc-02', $merc02);
//        $this->addReference('merc-03', $merc03);
//        $this->addReference('merc-04', $merc04);
//        $this->addReference('merc-05', $merc05);
//        $this->addReference('merc-06', $merc06);
//        $this->addReference('merc-07', $merc07);
//        $this->addReference('merc-08', $merc08);
//        $this->addReference('merc-09', $merc09);
//        $this->addReference('merc-10', $merc10);

    }

    public function getOrder()
    {
        return 4;
    }
}
