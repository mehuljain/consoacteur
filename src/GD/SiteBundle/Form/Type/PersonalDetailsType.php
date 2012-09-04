<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use GD\SiteBundle\Form\Extension\ChoiceList\ProfessionChoiceList;


class PersonalDetailsType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {

        $builder->add('firstName', 'text', array(
            'label' => 'userdetails.firstname.label',
            'required' => false,
            'attr' => array('class' => 'large_text'),
        ));
        $builder->add('lastName', 'text', array(    
            'label' => 'userdetails.lastname.label',
            'required' => false,
            'attr' => array('class' => 'large_text'),
        ));
        $builder->add('dateOfBirth', 'date', array(
            'label' => 'userdetails.dateofbirth.label', 
            'input'  => 'datetime',
            'years' => range(date('Y')-100,date('Y')-14),
            'format' => 'yyyy-MMMM-dd',
            'required' => false,
            'empty_value' => array('year' => 'year.label', 'month' => 'month.label', 'day' => 'day.label'),
            'empty_data'  => null
        ));
        $builder->add('profession', 'choice', array(
            'empty_value' => 'userdetails.professionemptyvalue.label',
            'empty_data'  => null,
            'required' => false,
            'choice_list' => new ProfessionChoiceList(),
            'label' => 'userdetails.profession.label',
        ));
  }

  public function getName() {
    return 'personalDetails';
  }

  public function getDefaultOptions(array $options) {
    return array(
        'virtual' => true,
    );
  }

}