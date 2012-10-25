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
//        $cbOffer01 = new Offer();
//        $cbOffer01->setName("Gains Migrations");
//        $cbOffer01->setMerchant($this->getReference('merc-01'));
//        $cbOffer01->setType(Offer::OFFER_TYPE_CASHBACK);
//        $cbOffer01->setTranslatableLocale("fr");
//        $cbOffer01->setDescription("Les gains migrations");
//        $cbOffer01->setProgramId("01");
//        $cbOffer01->setRedirectionUri("http://www.conso-acteur.com");        
//        $cbOffer01->setStartDate(new \DateTime());
//        $cbOffer01->setEndDate(new \DateTime());
//        $cbOffer01->setUserGainValue("100%");
//        $cbOffer01->setUserGainPercentage(100);
//        $cbOffer01->setIsCurrent(false);
//        $cbOffer01->setIsArchived(true);
//        $cbOffer01->setCreatedAt(new \DateTime());
//        $cbOffer01->setUpdatedAt(new \DateTime());
//        $cbOffer01->setArchivedAt(new \DateTime());        
//        $manager->persist($cbOffer01);
//
//        $manager->flush();
//        
//        $this->addReference('offer-01', $cbOffer01);

        $delim = ',';
        $escape = '"';
        $offersFile = dirname(__FILE__).'/'.'cboffers_second.csv';
        $fid = fopen($offersFile, 'rb');

        if ($fid === false) {
            echo "Could not load cashback offer";
            exit;
        }
      
        while (($ligne = fgetcsv($fid, 4096, $delim, $escape)) !== FALSE) {
            
            $cbOffer01 = new Offer();
            $cbOffer01->setName($ligne[1]);
            $cbOffer01->setMerchant($this->getReference($ligne[6]));
            $cbOffer01->setType(Offer::OFFER_TYPE_CASHBACK);
            $cbOffer01->setTranslatableLocale("fr");
            $cbOffer01->setDescription($ligne[5]);
            $cbOffer01->setProgramId($ligne[2]);
            $cbOffer01->setRedirectionUri($ligne[3]);
            $cbOffer01->setStartDate(new \DateTime('2012-10-10'));
            $cbOffer01->setEndDate(new \DateTime('2017-01-01'));
            $cbOffer01->setUserGainValue($ligne[4]);
            $cbOffer01->setUserGainPercentage(50);
            $cbOffer01->setIsCurrent(true);
            $cbOffer01->setIsArchived(false);
            $cbOffer01->setCreatedAt(new \DateTime('today'));
            $cbOffer01->setUpdatedAt(new \DateTime('today'));
            if($ligne[7] == 1){
            $cbOffer01->setCashbackValuePercentage($ligne[8]);
            }
            else {
            $cbOffer01->setCashbackValueAmount($ligne[8]); 
            }
            $manager->persist($cbOffer01);
            $manager->flush();
        }
        fclose($fid);
    }

    public function getOrder()
    {
        return 14;
    }
}
