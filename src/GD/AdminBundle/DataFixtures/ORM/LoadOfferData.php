<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Offer;
use Doctrine\Common\Persistence\ObjectManager;

class LoadOfferData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $cbOffer01 = new Offer();
        $cbOffer01->setName("Gains Migrations Offer");
        $cbOffer01->setMerchant($this->getReference('merc-01'));
        $cbOffer01->setType(Offer::OFFER_TYPE_CASHBACK);
        $cbOffer01->setTranslatableLocale("fr");
        $cbOffer01->setDescription("Les gains migrations");
        $cbOffer01->setProgramId("01");
        $cbOffer01->setRedirectionUri("http://www.conso-acteur.com");        
        $cbOffer01->setStartDate(new \DateTime('now'));
        $cbOffer01->setEndDate(new \DateTime('tomorrow'));
        $cbOffer01->setUserGainValue("100%");
        $cbOffer01->setUserGainPercentage(100);
        $cbOffer01->setIsCurrent(false);
        $cbOffer01->setIsArchived(true);
        $cbOffer01->setCreatedAt(new \DateTime());
        $cbOffer01->setUpdatedAt(new \DateTime());
        $cbOffer01->setArchivedAt(new \DateTime());        
        $manager->persist($cbOffer01);

        $manager->flush();
        
        $this->addReference('offer-01', $cbOffer01);

    }

    public function getOrder()
    {
        return 14;
    }
}
