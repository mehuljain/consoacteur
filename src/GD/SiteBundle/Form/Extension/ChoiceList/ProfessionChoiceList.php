<?php
namespace GD\SiteBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class ProfessionChoiceList implements ChoiceListInterface
{
    public function getChoices()
    {
        return array(
        '1' => 'profession.option.choice1',
        '2' => 'profession.option.choice2',
        '3' =>  'profession.option.choice3',
        '4' => 'profession.option.choice4',
        '5' => 'profession.option.choice5',
        '6' => 'profession.option.choice6',
        '7' => 'profession.option.choice7',
        '8' => 'profession.option.choice8',
        '9' => 'profession.option.choice9',
        '10'=>  'profession.option.choice10',
        '11' => 'profession.option.choice11',
        '12' => 'profession.option.choice12',
        '13' => 'profession.option.choice13',
        '14' => 'profession.option.choice14',
        '15' => 'profession.option.choice15',
        '16' => 'profession.option.choice16',
        '17' => 'profession.option.choice17');
    }
}
