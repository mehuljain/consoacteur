<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use GD\SiteBundle\Validator\UniqueEmail;
use GD\SiteBundle\Validator\UniqueReferralEmail;

class ReferFriendType extends AbstractType
{
      protected $translator;

      public function __construct(\Symfony\Bundle\FrameworkBundle\Translation\Translator $translator){
          $this->translator = $translator;
      }
      
      public function buildForm(FormBuilder $builder, array $options)
      {
        $defaultSubject = $this->translator->trans('referralmail.defaultsubject.text');
        
        $builder->add('email1', 'email',array(
            'required' => true,
            'label' => $this->translator->trans('referralpage.email.label',array('%number%' => 1)),
            'attr' => array('class' => 'large_text'),
        ));
        $builder->add('email2', 'email',array(
            'label' => $this->translator->trans('referralpage.email.label',array('%number%' => 2)),
            'required' => false,
            'attr' => array('class' => 'large_text'),
        ));
        $builder->add('email3', 'email',array(
            'label' => $this->translator->trans('referralpage.email.label',array('%number%' => 3)),
            'required' => false,
            'attr' => array('class' => 'large_text'),
        ));
        $builder->add('email4', 'email',array(
            'label' => $this->translator->trans('referralpage.email.label',array('%number%' => 4)),
            'required' => false,
            'attr' => array('class' => 'large_text'),
        ));
        $builder->add('email5', 'email',array(
            'label' => $this->translator->trans('referralpage.email.label',array('%number%' => 5)),
            'required' => false,
            'attr' => array('class' => 'large_text'),
        ));
        $builder->add('subject', 'text', array(
            'data' => $defaultSubject,
            'required' => true,
            'label' => $this->translator->trans('referralpage.subject.label'),
            'attr' => array('class' => 'large_text'),
        ));
        $builder->add('message', 'textarea', array(
            'required' => false,
            'label' => $this->translator->trans('referralpage.message.label'),
            'attr' => array('rows' => '6', 'cols' => '50'),
        ));
     
    }
    
    public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection( array(
            'fields' => array(
                'email1' => array(
                    new Email(),
                    new NotBlank(array(
                        'message' => $this->translator->trans('referral.email.not_blank',array(),'validators'),
                    )),
                    new UniqueEmail(array(
                        'property' => 'email',
                        'message' => $this->translator->trans('referral.email.already_registered',array(),'validators'),
                    )),
                    new UniqueReferralEmail(array(
                        'property' => 'referralEmail',
                        'message' => $this->translator->trans('referral.email.already_sent',array(),'validators'),
                    )),
                 ),
                'email2' => array(
                    new Email(),
                    new UniqueEmail(array(
                        'property' => 'email',
                        'message' => $this->translator->trans('referral.email.already_registered',array(),'validators'),
                    )),
                    new UniqueReferralEmail(array(
                        'property' => 'referralEmail',
                        'message' => $this->translator->trans('referral.email.already_sent',array(),'validators'),
                    )),
                 ),
                'email3' => array(
                    new Email(),
                    new UniqueEmail(array(
                        'property' => 'email',
                        'message' => $this->translator->trans('referral.email.already_registered',array(),'validators'),
                    )),
                    new UniqueReferralEmail(array(
                        'property' => 'referralEmail',
                        'message' => $this->translator->trans('referral.email.already_sent',array(),'validators'),
                    )),
                 ),
                'email4' => array(
                    new Email(),
                    new UniqueEmail(array(
                        'property' => 'email',
                        'message' => $this->translator->trans('referral.email.already_registered',array(),'validators'),
                    )),
                    new UniqueReferralEmail(array(
                        'property' => 'referralEmail',
                        'message' => $this->translator->trans('referral.email.already_sent',array(),'validators'),
                    )),
                 ),
                'email5' => array(
                    new Email(),
                    new UniqueEmail(array(
                        'property' => 'email',
                        'message' => $this->translator->trans('referral.email.already_registered',array(),'validators'),
                    )),
                    new UniqueReferralEmail(array(
                        'property' => 'referralEmail',
                        'message' => $this->translator->trans('referral.email.already_sent',array(),'validators'),
                    )),
                 ),
                'subject' => new NotBlank(),
            ),
            'allowExtraFields' => true,
            'allowMissingFields' => true,
        ));
        
        return array(
            'validation_constraint' => $collectionConstraint,
            'csrf_protection' => true,
        );
    }
    
    public function getName()
    {
        return 'referFriend';
    }
}