<?php

namespace GD\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class NewsletterType extends AbstractType
{
  
  public function buildForm(FormBuilder $builder, array $options)
  {
       $builder->add('email', 'email',array(
            'label' => 'newsletter.email.label',
            'attr' => array('class' => 'large_text'),            
            'required' => true,
            'error_bubbling' => false)
       );
  }
  
   public function getDefaultOptions(array $options)
   {
         return array('csrf_protection' => false,'validation_groups' => array('newsletter'));
   }

  public function getName()
  {
        return 'newsletter';
  }
}
