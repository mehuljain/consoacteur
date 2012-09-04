<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChangePasswordType extends AbstractType
{
    protected $translator;
    
    public function __construct(\Symfony\Bundle\FrameworkBundle\Translation\Translator $translator){
      
            $this->translator = $translator;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('current', 'password',array(
            'label' => 'changeemail.currentpassword.label',
            'attr' => array('class' => 'small_text'),
            'required' => true,
            'error_bubbling' => false)
        );

        $builder->add('new', 'repeated', array(
            'type' => 'password',
            'first_name' => $this->translator->trans('changepassword.newpassword.label'),
            'second_name' => $this->translator->trans('changepassword.confirmpassword.label'),
            'invalid_message' => "user.plainPassword.not_match",
            'options' => array('attr' => array('class' => 'small_text repeated')),
            'error_bubbling' => false,
            'required' => true,
        ));
        
     }
    
    public function getDefaultOptions(array $options)
    {

        return array(
            'data_class' => 'GD\SiteBundle\Form\Model\ChangePassword',
            'validation_groups'  => 'profile',
            'csrf_protection' => true
        );
    }

    public function getName()
    {
        return 'gd_user_password_change';
    }
}
