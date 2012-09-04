<?php

namespace GD\SiteBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class AccountClosureReasonChoiceList implements ChoiceListInterface {

  public function getChoices() {
    
    return array(
       '1' => 'account.closure.reason1',
       '2' => 'account.closure.reason2',
       '3' => 'account.closure.reason3',
       '4' => 'account.closure.reason4',
     );
  }

}