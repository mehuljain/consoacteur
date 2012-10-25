<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAdminUsersData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        $admin = new User();
//        $admin->setIsAdminUser(true);
//        $admin->setUsername('kevinc2b');
//        $admin->setFirstName('Kevin');
//        $admin->setLastName('Hatry');
//        $admin->setEmail('kevin.hatry@c2bsa.com');
//        $admin->setPlainPassword('kevinc2b');
//        $admin->addGroup($this->getReference('admin-group'));
//        $admin->setEnabled(true);
//        $manager->persist($admin);
//      
//        $manager->flush();

    }

    public function getOrder()
    {
        return 10;
    }
}