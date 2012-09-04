<?php

namespace GD\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
       $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'first_name' => 'New Password',
            'second_name' => 'Confirm New Password',
            'invalid_message' => "Passwords don't match",
            'options' => array('attr' => array('class' => 'small_text repeated')),
            'error_bubbling' => false,
        ));

     }
    
    public function getDefaultOptions(array $options)
    {
        return array('csrf_protection' => false);
    }

    public function getName()
    {
        return 'gd_user_password_reset';
    }
}
