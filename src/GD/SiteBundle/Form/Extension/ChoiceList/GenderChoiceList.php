<?php

namespace GD\SiteBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class GenderChoiceList implements ChoiceListInterface {

  public function getChoices() {
    return array(
        '1' => 'gender.option.choice1',
        '2' => 'gender.option.choice2',
    );
  }

}