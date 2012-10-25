<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTagData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        $tag01 = new Tag();
//        $tag01->setName("tag migration");
//        $tag01->addCategory($this->getReference('cat-01'));
//        $tag01->setTranslatableLocale("fr");
//        $tag01->setDescription("tag migration");
//        $tag01->setCreatedAt(new \DateTime('now'));
//        $tag01->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag01);
//        
//        $manager->flush();
//
//        $this->addReference('tag-01', $tag01);
//        
//        $delim = ',';
//        $escape = '"';
//        $tagsFile = dirname(__FILE__).'/'.'tags.csv';
//        $fid = fopen($tagsFile, 'rb');
//
//        if ($fid === false) {
//            echo "The tags data could not be loaded";
//            exit;
//        }
//      
//      while (($ligne = fgetcsv($fid, 1024, $delim, $escape)) !== FALSE) {
//          
//        $tag01 = new Tag();
//        $tag01->setName($ligne[0]);
//        $tag01->addCategory($this->getReference($ligne[1]));
//        $tag01->setTranslatableLocale("fr");
//        $tag01->setDescription($ligne[0]);
//        $tag01->setCreatedAt(new \DateTime('now'));
//        $tag01->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag01);
//        $manager->flush();
//        $this->addReference($ligne[2], $tag01);
//        
//      }
//      fclose($fid);
//      
   }

    public function getOrder()
    {
        return 2;
    }
}
