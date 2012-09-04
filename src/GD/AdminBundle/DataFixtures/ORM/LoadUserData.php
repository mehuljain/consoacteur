<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
//        $user01 = new User();
//        $user01->setUsername('bob');
//        $user01->setPlainPassword('bob');
//        $user01->setSalutation('Mr');
//        $user01->setFirstName('Bob');
//        $user01->setLastName('Cooper');
//        $user01->setEnabled(true);
//        $user01->setDateOfBirth(new \DateTime('01-04-1980'));
//        $user01->setProfession('Lawyer');
//        $user01->setGender(1);
//        $user01->setEnabled(true);
//        $user01->setApartmentNumber('#24');
//        $user01->setAddressLocation('31st Avenue');
//        $user01->setLocationName('Baker Street');
//        $user01->setComplementaryAddressDetails('');
//        $user01->setZipcode('95249');
//        $user01->setCity('San Andreas');
//        $user01->setCountry('Calaveras');
//        $user01->setPhoneHome('415-509-6995');
//        $user01->setPhoneMobile('415-509-2992');
//        $user01->setPhoneOffice('415-509-2334');
//        $user01->setEmail('bob.cooper@mail.com');
//        $user01->setReferralType('');
//        $user01->setNewsletterSubscription(1);
//        $user01->setShareContact(1);
//        $user01->setAccountClosureReason('');
//        $user01->setAccountClosureComment('');
//        $user01->setCreatedAt(new \DateTime());
//        $user01->setUpdatedAt(new \DateTime());
//        //$user01->setAdvertisementByType('0:1:2:3:4');
//        //$user01->setAdvertisementFrequency('Not more than 4 per week:Maximum 2 per week:Maximum 1 per week:Maximum 2 per month:Maximum 1 per month');
//        $manager->persist($user01);
//
//        $user02 = new User();
//        $user02->setUsername('mary');
//        $user02->setPlainPassword('mary');
//        $user02->setSalutation('Mrs');
//        $user02->setFirstName('Mary');
//        $user02->setLastName('Dsoza');
//        $user02->setEnabled(true);
//        $user02->setDateOfBirth(new \DateTime('07-05-1976'));
//        $user02->setProfession('Professor');
//        $user02->setGender(0);
//        $user02->setEnabled(true);
//        $user02->setApartmentNumber('#334');
//        $user02->setAddressLocation('1st Avenue');
//        $user02->setLocationName('Magic Street');
//        $user02->setComplementaryAddressDetails('');
//        $user02->setZipcode('95249');
//        $user02->setCity('San Andreas');
//        $user02->setCountry('Calaveras');
//        $user02->setPhoneHome('415-509-2395');
//        $user02->setPhoneMobile('415-509-2342');
//        $user02->setPhoneOffice('415-509-2034');
//        $user02->setEmail('mary.dsoza@mail.com');
//        $user02->setReferralType('');
//        $user02->setNewsletterSubscription(1);
//        $user02->setShareContact(1);
//        $user02->setAccountClosureReason('');
//        $user02->setAccountClosureComment('');
//        $user02->setCreatedAt(new \DateTime());
//        $user02->setUpdatedAt(new \DateTime());
//        //$user02->setAdvertisementByType('0:1:2:3:4');
//        //$user02->setAdvertisementFrequency('Not more than 4 per week:Maximum 2 per week,Maximum 1 per week:Maximum 2 per month:Maximum 1 per month');
//        $manager->persist($user02);
//
//        $manager->flush();
//        
//        $this->addReference('user-01', $user01);
//        $this->addReference('user-02', $user02);

    }

    public function getOrder()
    {
        return 6;
    }
}
