<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use GD\SiteBundle\Form\Extension\ChoiceList\SalutationChoiceList;
use GD\SiteBundle\Form\Extension\ChoiceList\ReferralChoiceList;
use GD\SiteBundle\Form\Extension\ChoiceList\GenderChoiceList;

use GD\AdminBundle\Entity\User;
  
class RegistrationType extends AbstractType
{
    protected $translator;
    
    public function __construct(\Symfony\Bundle\FrameworkBundle\Translation\Translator $translator){
      
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        //Mandatory Fields
        $builder->add('salutation', 'choice', array(
            'choice_list'   => new SalutationChoiceList(),
            'expanded' => true,
            'multiple' => false,
            'label' => 'userdetails.salutationrequired.label',
            'required' => true,
            'error_bubbling' => false,
        ));
        
        $builder->add('email', 'email', array(
            'attr' => array('class' => 'large_text'),
            'label' => 'userdetails.emailrequired.label',
            'required' => true,
            'error_bubbling' => false,
        ));
       
        // Login Details
        $builder->add('username', 'text' , array(
            'attr' => array('class' => 'small_text'),
            'label' => 'userdetails.usernamerequired.label',
            'required' => true,
            'error_bubbling' => false,
        ));
        
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'first_name' => $this->translator->trans('userdetails.passwordrequired.label'),
            'second_name' => $this->translator->trans('userdetails.confirmpasswordrequired.label'),
            'invalid_message' => 'user.plainPassword.not_match',
            'required' => true,
            'options' => array('attr' => array('class' => 'small_text repeated')),
            'error_bubbling' => false,
        ));
        
        //Personal Details
        $builder->add('subscribePersonal', new PersonalDetailsType());
        
        //gender field
        $builder->add('gender', 'choice', array(
            'choice_list'  => new GenderChoiceList(),
            'required'  => false,
            'multiple'  => false,
            'expanded' => true,
            'label' => 'userdetails.gender.label',
        ));
        //Address Details
        $builder->add('subscribeAddress', new AddressDetailsType());
        // Contact Details
        $builder->add('subscribeContact', new ContactDetailsType());
        
        // Referral Details
        $builder->add('referralType','choice',array(
            'empty_value' => 'advertisementdetails.emptyvalue.label',
            'choice_list' => new ReferralChoiceList(),
            'label' => 'userdetails.referraltype.label',
            'required' => false,
        ));
        $builder->add('sponsorshipCode','text',array(
            'label' => 'userdetails.sponsorcode.label',
            'attr' => array('class' => 'small_text'),
            'required' => false,
            'error_bubbling' => false,
        ));
        
        // Advertisement Details
        $builder->add('subscribeAds', new AdvertisementDetailsType());     
        
        // Security Details
        $builder->add('shareContact', 'choice', array(
            'choices'   => array('1' => 'Yes', '0' => 'No'),
            'expanded' => true,
            'label' => 'userdetails.sharecontact.label',
            'required' => false,
        ));
        
        $builder->add('hasAcceptedTermsAndConditions', 'checkbox', array(            
            'required' => true,
            'error_bubbling' => false,
        ));
        
    }
    
    public function getDefaultOptions(array $options)
    {
         return array('csrf_protection' => false,'validation_groups' => array('registration'));
    }

    public function getName()
    {
        return 'gd_user_registration';
    }
}
