<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use GD\SiteBundle\Form\Extension\ChoiceList\AddressLocationChoiceList;
use GD\SiteBundle\Form\Extension\ChoiceList\CountryChoiceList;

class AddressDetailsType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {

    $builder->add('apartmentNumber', 'text', array(
        'label' => 'addressdetails.apartmentNumber.label',
        'attr' => array('class' => 'small_text'),
        'required' => false,
    ));
    $builder->add('addressLocation', 'choice', array(
        'empty_value' => 'addressdetails.locationemptyvalue.label',
        'empty_data' => null,
        'choice_list' => new AddressLocationChoiceList(),
        'label' => 'addressdetails.location.label',
        'required' => false,
    ));
    $builder->add('locationName', 'text', array(
        'label' => 'addressdetails.locationname.label',
        'attr' => array('class' => 'small_text'),
        'required' => false,
    ));
    $builder->add('complementaryAddressDetails', 'textarea', array(
        'label' => 'addressdetails.complementaryAddressDetails.label',
        'attr' => array('class' => 'large_text'),
        'required' => false,
    ));
    $builder->add('zipcode', 'text', array(
        'label' => 'addressdetails.zipcode.label',
        'attr' => array('class' => 'small_text'),
        'required' => false,
    ));
    $builder->add('city', 'text', array(
        'label' => 'addressdetails.city.label',
        'attr' => array('class' => 'small_text'),
        'required' => false,
    ));
    $builder->add('country', 'choice', array(
        'choice_list' => new CountryChoiceList(),
        'empty_value' => 'addressdetails.countryemptyvalue.label',
        'empty_data' => null,
        'label' => 'addressdetails.country.label',
        'required' => false,
    ));
  }

  public function getName() {
    return 'addressDetails';
  }

  public function getDefaultOptions(array $options) {
    return array(
        'virtual' => true,
    );
  }

}