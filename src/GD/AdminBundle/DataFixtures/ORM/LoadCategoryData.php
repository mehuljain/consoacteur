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
//        $cat01 = new Category();
//        $cat01->setName("Category 01");
//        $cat01->setTranslatableLocale("en");
//        $cat01->setDescription("Description of Category 01");
//        $cat01->setCreatedAt(new \DateTime('now'));
//        $cat01->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($cat01);
//
//        $cat02 = new Category();
//        $cat02->setName("Category 02");
//        $cat02->setTranslatableLocale("en");
//        $cat02->setDescription("Description of Category 02");
//        $cat02->setCreatedAt(new \DateTime('now'));
//        $cat02->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($cat02);
//
//        $cat03 = new Category();
//        $cat03->setName("Category 03");
//        $cat03->setTranslatableLocale("en");
//        $cat03->setDescription("Description of Category 03");
//        $cat03->setCreatedAt(new \DateTime('now'));
//        $cat03->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($cat03);
//
//        $cat04 = new Category();
//        $cat04->setName("Category 04");
//        $cat04->setTranslatableLocale("en");
//        $cat04->setDescription("Description of Category 04");
//        $cat04->setCreatedAt(new \DateTime('now'));
//        $cat04->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($cat04);
//
//        $cat05 = new Category();
//        $cat05->setName("Category 05");
//        $cat05->setTranslatableLocale("en");
//        $cat05->setDescription("Description of Category 05");
//        $cat05->setCreatedAt(new \DateTime('now'));
//        $cat05->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($cat05);
//
//        $manager->flush();
//
//        $this->addReference('cat-01', $cat01);
//        $this->addReference('cat-02', $cat02);
//        $this->addReference('cat-03', $cat03);
//        $this->addReference('cat-04', $cat04);
//        $this->addReference('cat-05', $cat05);
    }

    public function getOrder()
    {
        return 1;
    }
}
