<?php

namespace GD\SiteBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;


class NewRequestChoiceList implements ChoiceListInterface {

  public function getChoices() {
    return array(
       '1' => 'problem.statement.choice1',
       '2' => 'problem.statement.choice2',
       '3' => 'problem.statement.choice3',
       '4' => 'problem.statement.choice4',
       '5' => 'problem.statement.choice5',
       '6' => 'problem.statement.choice6',
     );
  }

}