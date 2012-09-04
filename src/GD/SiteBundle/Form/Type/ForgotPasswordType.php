<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ForgotPasswordType extends AbstractType
{
    protected $translator;
    
    public function __construct(\Symfony\Bundle\FrameworkBundle\Translation\Translator $translator){
      
            $this->translator = $translator;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
       $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'first_name' => $this->translator->trans('changepassword.newpassword.label'),
            'second_name' => $this->translator->trans('changepassword.confirmpassword.label'),
            'invalid_message' => "user.plainPassword.not_match",
            'options' => array('attr' => array('class' => 'small_text repeated')),
            'error_bubbling' => false,
        ));

     }
    
    public function getDefaultOptions(array $options)
    {

        return array(
            'validation_groups'  => 'resetpassword',
            'csrf_protection' => true,
        );
    }

    public function getName()
    {
        return 'gd_user_password_reset';
    }
}
