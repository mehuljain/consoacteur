<?php

namespace GD\SiteBundle\Form\Type\Withdrawal;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChequeWithdrawalType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('chequePayee',null,array('label' => 'chequewithdrawal.payee.label',
                    'required' => true,));        
        $builder->add('address', 'textarea',array('label' => 'chequewithdrawal.useraddress.label',
                    'required' => true,));
        $builder->add('amount', null, array('attr' => array('readonly' => true),
            'label' => 'withdrawal.amount.label'));
        $builder->add('code',null,array(
                    'label' => 'withdrawal.code.label',
                    'required' => true,));        
    }
    
    public function getDefaultOptions(array $options)
    {
         return array('csrf_protection' => true,'validation_groups' => array('cheque'));
    }

    public function getName()
    {
        return 'cheque_withdrawal';
    }

}
