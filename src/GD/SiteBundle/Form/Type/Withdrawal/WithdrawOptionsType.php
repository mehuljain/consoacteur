<?php

namespace GD\SiteBundle\Form\Type\Withdrawal;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use GD\AdminBundle\Entity\Withdrawal;
use Symfony\Component\Validator\Constraints\Min;

class WithdrawOptionsType extends AbstractType
{
    protected $bankType;
    protected $bank_withdrawal_limit;
    protected $cheque_withdrawal_limit;
    protected $paypal_withdrawal_limit;
    protected $translator;    
    private $currency;

    public function __construct($bankType, $currency, \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator, $withdrawallimits = array())
    {
        if (Withdrawal::WITHDRAWAL_TYPE_BANK_1 !== $bankType && Withdrawal::WITHDRAWAL_TYPE_BANK_2 !== $bankType) {
            throw new \Exception("Undefined bank type option");
        }
        $this->bankType = $bankType;
        $this->bank_withdrawal_limit = $withdrawallimits['bank'];
        $this->cheque_withdrawal_limit = $withdrawallimits['cheque'];
        $this->paypal_withdrawal_limit = $withdrawallimits['paypal'];
        $this->currency = $currency;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $choices = array(
            $this->bankType => $this->translator->trans('userearnings.bank.text').' ( '.$this->translator->trans('userearnings.minimumamount.text',array('%amount%'=> $this->bank_withdrawal_limit)).$this->currency.' ) ',
            Withdrawal::WITHDRAWAL_TYPE_PAYPAL => $this->translator->trans('userearnings.paypal.text').' ( '.$this->translator->trans('userearnings.minimumamount.text',array('%amount%'=> $this->paypal_withdrawal_limit)).$this->currency.' ) ',
            Withdrawal::WITHDRAWAL_TYPE_CHEQUE => $this->translator->trans('userearnings.cheque.text').' ( '.$this->translator->trans('userearnings.minimumamount.text',array('%amount%'=> $this->cheque_withdrawal_limit)).$this->currency.' ) ',
        );

        $builder->add('type', 'choice', array(
                'choices'   => $choices,
                'required'  => true,
                'expanded' => true,
                'empty_data' => NULL,
            )
        );
    }

    public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'type' => new \Symfony\Component\Validator\Constraints\NotNull()
        ));

        return array('validation_constraint' => $collectionConstraint);
    }

    public function getName()
    {
        return 'withdraw_options';
    }
}
