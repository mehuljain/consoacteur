<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PasswordResetRequestType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {
    
    $builder->add('usernameOrEmail', 'text', array(
        'attr' => array('class' => 'large_text'),
        'label' => 'passwordreset.usernameEmail.label',
        'error_bubbling' => false)
    );
  }

  public function getDefaultOptions(array $options) {
    return array(
        'csrf_protection' => true
    );
  }

  public function getName() {
    return 'gd_user_password_reset_request';
  }
}
