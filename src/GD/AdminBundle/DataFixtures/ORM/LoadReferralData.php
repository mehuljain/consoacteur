<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Referral;
use Doctrine\Common\Persistence\ObjectManager;

class LoadReferralData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {


    }

    public function getOrder()
    {
        return 15;
    }


}
