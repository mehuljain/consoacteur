<?php

namespace GD\SiteBundle\Form\Type\Withdrawal;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use GD\AdminBundle\Entity\Withdrawal;

class BankWithdrawalType extends AbstractType
{
    protected $bankType;

    public function __construct($bankType)
    {
        if (Withdrawal::WITHDRAWAL_TYPE_BANK_1 != $bankType && Withdrawal::WITHDRAWAL_TYPE_BANK_2 != $bankType) {
            throw new \Exception("Undefined bank type option");
        }
        $this->bankType = $bankType;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($this->bankType == Withdrawal::WITHDRAWAL_TYPE_BANK_1) {
            $builder
                ->add('iban',NULL, array(
                    'label' => 'bankwithdrawal.iban.label',
                    'required' => true,))
                ->add('address', 'textarea', array(
                    'label' => 'bankwithdrawal.address.label',
                    'required' => true,))
                ->add('swiftCode',NULL,array(
                    'label' => 'bankwithdrawal.swiftcode.label',
                    'required' => true,))
            ;
        } elseif ($this->bankType == Withdrawal::WITHDRAWAL_TYPE_BANK_2) {
            $builder
                ->add('bankName',NULL, array(
                    'label' => 'bankwithdrawal.bankname.label',
                    'required' => true,))
                // Making country a string for now--Need to update when a country uses this Withdrawal bank type
                ->add('country',null,array(
                    'label' => 'bankwithdrawal.bankcountry.label',
                    'required' => true,))
                ->add('address',null, array(
                    'label' => 'bankwithdrawal.address.label',
                    'required' => true,))
                ->add('accountNumber',null, array(
                    'label' => 'bankwithdrawal.accountnumber.label',
                    'required' => true,))
            ;
        }
        $builder->add('amount', null, array('attr' => array('readonly' => true),
            'label' => 'withdrawal.amount.label'));
        $builder->add('code',null,array(
                    'label' => 'withdrawal.code.label',
                    'required' => true));
    }

    public function getDefaultOptions(array $options)
    {
        if($this->bankType == 2){
           return array('csrf_protection' => true,'validation_groups' => array('bank_type2'));
        }
            return array('csrf_protection' => true,'validation_groups' => array('bank_type1'));
    }
    
    public function getName()
    {
        return 'bank_withdrawal';
    }

}
