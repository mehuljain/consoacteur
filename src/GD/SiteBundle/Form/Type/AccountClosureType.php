<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use GD\SiteBundle\Form\Extension\ChoiceList\AccountClosureReasonChoiceList;

class AccountClosureType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('reason','choice',array(
            'label' => 'accountclose.reason.label',
            'choice_list'  => new AccountClosureReasonChoiceList(),
            'empty_value' => 'accountclose.reasonemptyvalue.label',
            'required'  => true,
        ));
        
        $builder->add('message','textarea',array(
            'label' => 'accountclose.message.label',
            'required' => true,
            'attr' => array('rows' => '5', 'cols' => '40'),
        ));
        
        $builder->add('confirm', 'checkbox', array(
            'label' => false,
            'required' => true,
        ));
        
    }
    
    public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
                'fields' => array(
                    'reason' =>  new NotBlank(),
                    'message' => new NotBlank(),
                    'confirm' => new NotBlank( array(
                    'message' => 'user.accountCloseConfirm.not_blank',
                    )),
                ),
                'allowExtraFields' => true,
        ));
        
        return array(
            'validation_constraint' => $collectionConstraint,
            'csrf_protection' => true,
        );
    }
    
    public function getName()
    {
        return 'accountClosure';
    }
}