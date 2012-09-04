<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Tag;
use GD\AdminBundle\Entity\AffiliatePartner;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAffiliatePartnerData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $affiliatePartner01 = new AffiliatePartner();
        $affiliatePartner01->setName('netaffiliation');
        $affiliatePartner01->setCodename('netaff');
        $affiliatePartner01->setCreatedAt(new \DateTime('now'));
        $affiliatePartner01->setUpdatedAt(new \DateTime('now'));
        $manager->persist($affiliatePartner01);
        
        $affiliatePartner02 = new AffiliatePartner();
        $affiliatePartner02->setName('directSource');
        $affiliatePartner02->setCodename('direct');
        $affiliatePartner02->setCreatedAt(new \DateTime('now'));
        $affiliatePartner02->setUpdatedAt(new \DateTime('now'));
        $manager->persist($affiliatePartner02);
        
        $affiliatePartner03 = new AffiliatePartner();
        $affiliatePartner03->setName('zanox');
        $affiliatePartner03->setCodename('zanox');
        $affiliatePartner03->setCreatedAt(new \DateTime('now'));
        $affiliatePartner03->setUpdatedAt(new \DateTime('now'));        
        $manager->persist($affiliatePartner03);
        
        $affiliatePartner04 = new AffiliatePartner();
        $affiliatePartner04->setName('tradedoubler');
        $affiliatePartner04->setCodename('tradedoubler');
        $affiliatePartner04->setCreatedAt(new \DateTime('now'));
        $affiliatePartner04->setUpdatedAt(new \DateTime('now'));
        $manager->persist($affiliatePartner04);
        
        $affiliatePartner05 = new AffiliatePartner();
        $affiliatePartner05->setName('public-idees');
        $affiliatePartner05->setCodename('publicidees');
        $affiliatePartner05->setCreatedAt(new \DateTime('now'));
        $affiliatePartner05->setUpdatedAt(new \DateTime('now'));
        $manager->persist($affiliatePartner05);

        $manager->flush();

        $this->addReference('affiliatePartner-01', $affiliatePartner01);
        $this->addReference('affiliatePartner-02', $affiliatePartner02);
        $this->addReference('affiliatePartner-03', $affiliatePartner03);
        $this->addReference('affiliatePartner-04', $affiliatePartner04);
        $this->addReference('affiliatePartner-05', $affiliatePartner05);
    }

    public function getOrder()
    {
        return 3;
    }
}
