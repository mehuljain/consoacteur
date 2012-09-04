<?php

namespace GD\SiteBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use GD\AdminBundle\Entity\User;

class AdvertisementChoiceList implements ChoiceListInterface {

  public function getChoices() {
    return array(
       User::ADVERTISEMENT_MAXIMUM_4_PER_MONTH => 'advertisement.option.choice1',
       User::ADVERTISEMENT_MAXIMUM_2_PER_MONTH => 'advertisement.option.choice2',
       User::ADVERTISEMENT_MAXIMUM_1_PER_MONTH => 'advertisement.option.choice3',
       User::ADVERTISEMENT_AS_NEEDED_TO_BE_BEST_INFORMED  => 'advertisement.option.choice4',
       User::ADVERTISEMENT_NONE    => 'advertisement.option.choice5',
    );
  }

}