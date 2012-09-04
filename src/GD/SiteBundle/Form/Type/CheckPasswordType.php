<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CheckPasswordType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {
    
    $builder->add('current', 'password', array(
        'attr' => array('class' => 'large_text'),
        'label' => 'checkpassword.currentpassword.label',
        'error_bubbling' => false)
    );
  }

  public function getDefaultOptions(array $options) {
    return array(
        'csrf_protection' => true,
        'validation_groups' => array('profile'),
    );
  }

  public function getName() {
    return 'gd_user_password_check';
  }
}
