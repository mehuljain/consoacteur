<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use GD\SiteBundle\Form\Extension\ChoiceList\AdvertisementChoiceList;


class AdvertisementDetailsType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {

       $builder->add('advertisementByEmail','choice',array(
                'choice_list'  => new AdvertisementChoiceList(),
                'empty_value' => 'advertisementdetails.emptyvalue.label',
                'label' => 'advertisementdetails.emailtype.label',
                'required' => false,
        ));
        $builder->add('advertisementByPost','choice',array(
                'choice_list'  => new AdvertisementChoiceList(),
                'empty_value' => 'advertisementdetails.emptyvalue.label',
                'label' => 'advertisementdetails.posttype.label',
                'required' => false,
        ));
        $builder->add('advertisementByTelephone','choice',array(
                'choice_list'  => new AdvertisementChoiceList(),
                'empty_value' => 'advertisementdetails.emptyvalue.label',
                'label' => 'advertisementdetails.telephonetype.label',
                'required' => false,
        ));
        $builder->add('advertisementBySms','choice',array(
                'choice_list'  => new AdvertisementChoiceList(),
                'empty_value' => 'advertisementdetails.emptyvalue.label',
                'label' => 'advertisementdetails.smstype.label',
                'required' => false,
        ));
  }

  public function getName() {
    return 'advertisementDetails';
  }

  public function getDefaultOptions(array $options) {
    return array(
        'virtual' => true,
    );
  }

}