<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContactDetailsType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {

    $builder->add('phoneHome', 'text', array(
        'label' => 'contactdetails.homephone.label',
        'attr' => array('class' => 'large_text'),
        'required' => false,
    ));
    $builder->add('phoneMobile', 'text', array(
        'label' => 'contactdetails.mobilephone.label',
        'attr' => array('class' => 'large_text'),
        'required' => false,
    ));
    $builder->add('phoneOffice', 'text', array(
        'label' => 'contactdetails.officephone.label',
        'attr' => array('class' => 'large_text'),
        'required' => false,
    ));
  }

  public function getName() {
    return 'contactDetails';
  }

  public function getDefaultOptions(array $options) {
    return array(
        'virtual' => true,
    );
  }

}