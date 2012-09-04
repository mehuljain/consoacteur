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
//        $tag01->setName("Tag 01");
//        $tag01->addCategory($this->getReference('cat-01'));
//        $tag01->addCategory($this->getReference('cat-02'));
//        $tag01->addCategory($this->getReference('cat-03'));
//        $tag01->setTranslatableLocale("en");
//        $tag01->setDescription("Some Description");
//        $tag01->setCreatedAt(new \DateTime('now'));
//        $tag01->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag01);
//
//        $tag02 = new Tag();
//        $tag02->setName("Tag 02");
//        $tag02->addCategory($this->getReference('cat-02'));
//        $tag02->setTranslatableLocale("en");
//        $tag02->setDescription("Some Description");
//        $tag02->setCreatedAt(new \DateTime('now'));
//        $tag02->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag02);
//
//        $tag03 = new Tag();
//        $tag03->setName("Tag 03");
//        $tag03->addCategory($this->getReference('cat-02'));
//        $tag03->addCategory($this->getReference('cat-05'));
//        $tag03->setTranslatableLocale("en");
//        $tag03->setDescription("Some Description");
//        $tag03->setCreatedAt(new \DateTime('now'));
//        $tag03->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag03);
//
//        $tag04 = new Tag();
//        $tag04->setName("Tag 04");
//        $tag04->addCategory($this->getReference('cat-03'));
//        $tag04->addCategory($this->getReference('cat-04'));
//        $tag04->addCategory($this->getReference('cat-05'));
//        $tag04->setTranslatableLocale("en");
//        $tag04->setDescription("Some Description");
//        $tag04->setCreatedAt(new \DateTime('now'));
//        $tag04->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag04);
//
//        $tag05 = new Tag();
//        $tag05->setName("Tag 05");
//        $tag05->addCategory($this->getReference('cat-02'));
//        $tag05->addCategory($this->getReference('cat-03'));
//        $tag05->addCategory($this->getReference('cat-04'));
//        $tag05->setTranslatableLocale("en");
//        $tag05->setDescription("Some Description");
//        $tag05->setCreatedAt(new \DateTime('now'));
//        $tag05->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag05);
//
//        $tag06 = new Tag();
//        $tag06->setName("Tag 06");
//        $tag06->addCategory($this->getReference('cat-03'));
//        $tag06->setTranslatableLocale("fr");
//        $tag06->setDescription("Some Description");
//        $tag06->setCreatedAt(new \DateTime('now'));
//        $tag06->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag06);
//
//        $tag07 = new Tag();
//        $tag07->setName("Tag 07");
//        $tag07->addCategory($this->getReference('cat-01'));
//        $tag07->addCategory($this->getReference('cat-03'));
//        $tag07->setTranslatableLocale("fr");
//        $tag07->setDescription("Some Description");
//        $tag07->setCreatedAt(new \DateTime('now'));
//        $tag07->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag07);
//
//        $tag08 = new Tag();
//        $tag08->setName("Tag 08");
//        $tag08->addCategory($this->getReference('cat-01'));
//        $tag08->addCategory($this->getReference('cat-03'));
//        $tag08->addCategory($this->getReference('cat-05'));
//        $tag08->setTranslatableLocale("fr");
//        $tag08->setDescription("Some Description");
//        $tag08->setCreatedAt(new \DateTime('now'));
//        $tag08->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag08);
//
//        $tag09 = new Tag();
//        $tag09->setName("Tag 09");
//        $tag09->addCategory($this->getReference('cat-01'));
//        $tag09->addCategory($this->getReference('cat-02'));
//        $tag09->addCategory($this->getReference('cat-04'));
//        $tag09->setTranslatableLocale("fr");
//        $tag09->setDescription("Some Description");
//        $tag09->setCreatedAt(new \DateTime('now'));
//        $tag09->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag09);
//
//        $tag10 = new Tag();
//        $tag10->setName("Tag 10");
//        $tag10->addCategory($this->getReference('cat-01'));
//        $tag10->addCategory($this->getReference('cat-02'));
//        $tag10->addCategory($this->getReference('cat-03'));
//        $tag10->setTranslatableLocale("fr");
//        $tag10->setDescription("Some Description");
//        $tag10->setCreatedAt(new \DateTime('now'));
//        $tag10->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag10);
//
//        $tag11 = new Tag();
//        $tag11->setName("Tag 11");
//        $tag11->addCategory($this->getReference('cat-01'));
//        $tag11->addCategory($this->getReference('cat-04'));
//        $tag11->setTranslatableLocale("fr");
//        $tag11->setDescription("Some Description");
//        $tag11->setCreatedAt(new \DateTime('now'));
//        $tag11->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag11);
//
//        $tag12 = new Tag();
//        $tag12->setName("Tag 12");
//        $tag12->addCategory($this->getReference('cat-02'));
//        $tag12->addCategory($this->getReference('cat-03'));
//        $tag12->addCategory($this->getReference('cat-05'));
//        $tag12->setTranslatableLocale("en");
//        $tag12->setDescription("Some Description");
//        $tag12->setCreatedAt(new \DateTime('now'));
//        $tag12->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag12);
//
//        $tag13 = new Tag();
//        $tag13->setName("Tag 13");
//        $tag13->addCategory($this->getReference('cat-01'));
//        $tag13->addCategory($this->getReference('cat-02'));
//        $tag13->addCategory($this->getReference('cat-03'));
//        $tag13->addCategory($this->getReference('cat-04'));
//        $tag13->setTranslatableLocale("en");
//        $tag13->setDescription("Some Description");
//        $tag13->setCreatedAt(new \DateTime('now'));
//        $tag13->setUpdatedAt(new \DateTime('now'));
//        $manager->persist($tag13);
//
//        $manager->flush();
//
//        $this->addReference('tag-01', $tag01);
//        $this->addReference('tag-02', $tag02);
//        $this->addReference('tag-03', $tag03);
//        $this->addReference('tag-04', $tag04);
//        $this->addReference('tag-05', $tag05);
//        $this->addReference('tag-06', $tag06);
//        $this->addReference('tag-07', $tag07);
//        $this->addReference('tag-08', $tag08);
//        $this->addReference('tag-09', $tag09);
//        $this->addReference('tag-10', $tag10);
//        $this->addReference('tag-11', $tag11);
//        $this->addReference('tag-12', $tag12);
//        $this->addReference('tag-13', $tag13);

    }

    public function getOrder()
    {
        return 2;
    }
}
