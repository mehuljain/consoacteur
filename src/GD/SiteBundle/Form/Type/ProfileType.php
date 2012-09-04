<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use GD\SiteBundle\Form\Extension\ChoiceList\SalutationChoiceList;
use GD\SiteBundle\Form\Extension\ChoiceList\GenderChoiceList;
use GD\AdminBundle\Entity\User;

class ProfileType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {
    // Personal Details
    $builder->add('salutation', 'choice', array(
        'choice_list' => new SalutationChoiceList(),
        'expanded' => true,
        'multiple' => false,
        'label' => 'userdetails.salutationrequired.label',
        'required' => true,
        'error_bubbling' => false,
    ));

    $builder->add('profilePersonal', new PersonalDetailsType());
    //gender field
    $builder->add('gender', 'choice', array(
        'choice_list' => new GenderChoiceList(),
        'required' => false,
        'multiple' => false,
        'expanded' => true,
        'label' => 'userdetails.gender.label',
    ));

    // Address Details
    $builder->add('profileAddress', new AddressDetailsType());

    // Contact Details
    $builder->add('profileContact', new ContactDetailsType());
    $builder->add('email', 'email', array(
        'read_only' => true,
        'required' => true,
        'label' => 'userdetails.emailrequired.label',
        'attr' => array('class' => 'large_text'),
    ));

    // Login Details
    $builder->add('username', 'text', array('label' => 'userdetails.usernamerequired.label', 
        'read_only' => true, 
        'attr' => array('class' => 'small_text'),));

    //Advertisement Details
    $builder->add('profileAds', new AdvertisementDetailsType());

    $builder->add('newsletterSubscription', 'checkbox', array(
        'label' => 'userdetails.newsletter.label',
        'required' => false,
    ));
    // Security Details
    $builder->add('shareContact', 'choice', array(
        'choices' => array('1' => 'Yes', '0' => 'No'),
        'expanded' => true,
        'label' => 'userdetails.sharecontact.label',
        'required' => false,
    ));
  }

  public function getDefaultOptions(array $options) {
    return array(
        'csrf_protection' => false,
        'validation_groups' => array('profile'));
  }

  public function getName() {
    return 'profile';
  }

}
