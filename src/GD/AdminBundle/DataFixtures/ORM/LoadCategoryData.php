<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//      $delim = ',';
//      $escape = '"';
//      $categoriesFile = dirname(__FILE__).'/'.'categories.csv';
//      $fid = fopen($categoriesFile, 'rb');
//      
//      $cat01 = new Category();
//      $cat01->setName("category");
//      $cat01->setTranslatableLocale("fr");
//      $cat01->setDescription("old records");
//      $cat01->setCreatedAt(new \DateTime('now'));
//      $cat01->setUpdatedAt(new \DateTime('now'));
//      $manager->persist($cat01);
//      $manager->flush();
//      $this->addReference('cat-01', $cat01);  
//
//      if ($fid === false) {
//          echo "The categories data could not be loaded";
//          exit;
//      }
//      
//            
//      while (($ligne = fgetcsv($fid, 1024, $delim, $escape)) !== FALSE) {
//          
//              $cat01 = new Category();
//              $cat01->setName($ligne[0]);
//              $cat01->setTranslatableLocale("fr");
//              $cat01->setDescription($ligne[0]);
//              $cat01->setCreatedAt(new \DateTime('now'));
//              $cat01->setUpdatedAt(new \DateTime('now'));
//              $manager->persist($cat01);
//              $manager->flush();
//              $this->addReference($ligne[1], $cat01);
//      }
//        fclose($fid);        
  }

    public function getOrder()
    {
        return 1;
    }
}
