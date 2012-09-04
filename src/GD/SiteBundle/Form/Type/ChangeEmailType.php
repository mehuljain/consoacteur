<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use GD\AdminBundle\Entity\User;

class ChangeEmailType extends AbstractType
{
    protected $translator;
    
    public function __construct(\Symfony\Bundle\FrameworkBundle\Translation\Translator $translator){
      
            $this->translator = $translator;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    { 
      $builder->add('current', 'password',array(
            'label' => 'changeemail.currentpassword.label',
            'attr' => array('class' => 'large_text'),            
            'required' => true,
            'error_bubbling' => false)
       );
      
      $builder->add('new', 'repeated', array(
                    'type' => 'email',                        
                    'first_name' => $this->translator->trans('changeemail.newemail.label'),
                    'second_name' => $this->translator->trans('changeemail.confirmemail.label'),
                    'invalid_message' => 'user.email.not_match',
                    'options' => array( 'attr' => array('class' => 'large_text repeated')),
                    'required' => true,
                    ));
        
    }
    
    public function getDefaultOptions(array $options)
    {

       return array('data_class' => 'GD\SiteBundle\Form\Model\ChangeEmail',
                    'validation_groups'  => 'profile',
                    'csrf_protection' => true);
    }

    public function getName()
    {
        return 'gd_user_email_reset';
    }
}
