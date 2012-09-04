<?php

namespace GD\SiteBundle\Form\Type\Withdrawal;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PaypalWithdrawalType extends AbstractType
{
  protected $translator;
  
  public function __construct(\Symfony\Bundle\FrameworkBundle\Translation\Translator $translator){
    $this->translator = $translator;
  }
  
  public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('email', 'repeated', array(
            'type' => 'email',
            'first_name' => $this->translator->trans('paypalwithdrawal.email.label'),
            'second_name' => $this->translator->trans('paypalwithdrawal.confirmemail.label'),
            'invalid_message' => 'user.email.not_match',
            'required' => true,
        ));
        
        $builder->add('amount', null, array('attr' => array('readonly' => true),
            'label' => 'withdrawal.amount.label'));
        $builder->add('code',null,array(
                    'label' => 'withdrawal.code.label',
                    'required' => true,));
    }
    
    public function getDefaultOptions(array $options)
    {
         return array('csrf_protection' => true,'validation_groups' => array('paypal'));
    }

    public function getName()
    {
        return 'paypal_withdrawal';
    }

}
