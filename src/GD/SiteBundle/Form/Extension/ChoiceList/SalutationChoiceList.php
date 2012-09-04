<?php
namespace GD\SiteBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;


class SalutationChoiceList implements ChoiceListInterface
{
    public function getChoices()
    {
        return array(
        '1' => 'salutation.option.choice1',
        '2' => 'salutation.option.choice2',
        '3' => 'salutation.option.choice3',        
);    

    }
}
