<?php

namespace GD\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
       //TODO : Verify if we have to delete this file ??
        $builder->add('ratings', 'radio');
        $builder->add('comment', 'textarea');
    }

    public function getName()
    {
        return 'feedback';
    }
}
