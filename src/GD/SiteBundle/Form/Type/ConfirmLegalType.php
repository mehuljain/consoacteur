<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConfirmLegalType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
      
        $builder->add('confirm', 'checkbox', array(
            'label' => false,
            'required' => true,
        ));
        
    }
    
    public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
                'fields' => array(
                    'confirm' => new NotBlank( array(
                    'message' => 'user.hasAcceptedTermsAndConditions.not_blank',
                    )),
                ),
                'allowExtraFields' => true,
        ));
        
        return array(
            'validation_constraint' => $collectionConstraint,
            'csrf_protection' => true,
        );
    }
    
    public function getName()
    {
        return 'gd_confirm_legal';
    }
}