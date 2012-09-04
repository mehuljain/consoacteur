<?php

namespace GD\SiteBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class ReferralChoiceList implements ChoiceListInterface {

  public function getChoices() {
    return array(
        '1' => 'referral.option.choice1',
        '2' => 'referral.option.choice2',
        '3' => 'referral.option.choice3',
        '4' => 'referral.option.choice4',
        '5' => 'referral.option.choice5',
        '6' => 'referral.option.choice6',        
    );
  }

}