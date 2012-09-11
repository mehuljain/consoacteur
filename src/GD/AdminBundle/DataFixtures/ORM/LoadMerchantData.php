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
        $merc01 = new Merchant();
        $merc01->setName("Gains Migrations");
        $merc01->setTitle("Gains Migrations");
        $merc01->setDescription("Gains migrations");
        $merc01->setDefaultAverageFeedback(3);
        $merc01->setIsActive(false);
        $merc01->setCreatedAt(new \DateTime());
        $merc01->setUpdatedAt(new \DateTime());
        $merc01->setPrimaryCategory($this->getReference('cat-01'));
        $merc01->setPrimaryTag($this->getReference('tag-01'));
        $merc01->setTranslatableLocale("fr");
        $merc01->addTag($this->getReference('tag-01'));
        $merc01->addCategory($this->getReference('cat-01'));
        $merc01->setAffiliatePartner($this->getReference('affiliatePartner-02'));
        $manager->persist($merc01);
 
        $manager->flush();

        $this->addReference('merc-01', $merc01);
        
        $delim = ',';
        $escape = '"';
        $merchantsFile = dirname(__FILE__).'/'.'merchants.csv';
        $fid = fopen($merchantsFile, 'rb');

        if ($fid === false) {
            echo "Failed to load merchant fixtures";
            exit;
        }
      
        while (($ligne = fgetcsv($fid, 1024, $delim, $escape)) !== FALSE) {
            
            $merc01 = new Merchant();
            $merc01->setName($ligne[0]);
            $merc01->setTitle($ligne[1]);
            $merc01->setDescription($ligne[2]);
            $merc01->setImage($ligne[6]);
            $merc01->setDefaultAverageFeedback(3);
            $merc01->setIsActive(true);
            $merc01->setCreatedAt(new \DateTime());
            $merc01->setUpdatedAt(new \DateTime());
            $merc01->setPrimaryCategory($this->getReference($ligne[3]));
            $merc01->setPrimaryTag($this->getReference($ligne[4]));
            $merc01->setTranslatableLocale("fr");
            $merc01->addTag($this->getReference($ligne[4]));
            $merc01->addCategory($this->getReference($ligne[3]));
            $merc01->setAffiliatePartner($this->getReference('affiliatePartner-01'));
            $manager->persist($merc01);
            $manager->flush();
            $this->addReference($ligne[5], $merc01);
        }
        fclose($fid);
    }

    public function getOrder()
    {
        return 4;
    }
}
