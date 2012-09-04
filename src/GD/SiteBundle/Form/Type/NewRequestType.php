<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Choice;
use GD\SiteBundle\Form\Extension\ChoiceList\NewRequestChoiceList;

class NewRequestType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('problem','choice',array(
            'label' => 'newrequest.problemtype.label',
            'choice_list'  => new NewRequestChoiceList(),
            'empty_value' => 'newrequest.problememptyvalue.label',
            'required'  => true,
        ));
        $builder->add('subject','text',array(
            'label' => 'newrequest.subject.label',
            'required' => true,
            'attr' => array('class' => 'large_text'),            
        ));
        $builder->add('message','textarea',array(
            'label' => 'newrequest.message.label',
            'required' => true,
            'attr' => array('rows' => '5', 'cols' => '40'),
        ));
        
    }
    
    public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'problem' => new NotBlank(),
            'subject' => new NotBlank(),
            'message' => new NotBlank(),
        ));
        
        return array(
            'validation_constraint' => $collectionConstraint,
            'csrf_protection' => true,
        );
    }
    
    public function getName()
    {
        return 'gd_new_request';
    }
}