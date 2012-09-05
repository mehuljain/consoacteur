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
        $cat01 = new Category();
        $cat01->setName("category");
        $cat01->setTranslatableLocale("fr");
        $cat01->setDescription("old records");
        $cat01->setCreatedAt(new \DateTime('now'));
        $cat01->setUpdatedAt(new \DateTime('now'));
        $manager->persist($cat01);
        
        $manager->flush();

        $this->addReference('cat-01', $cat01);
    }

    public function getOrder()
    {
        return 1;
    }
}
