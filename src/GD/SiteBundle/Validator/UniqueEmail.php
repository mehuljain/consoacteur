<?php

namespace GD\SiteBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation 
 */
class UniqueEmail extends Constraint {

  public $message = 'This mail has already been registered';
  public $property;

  public function validatedBy() {
    return 'validator.uniqueemail';
  }
  
  public function requiredOptions() {
    return array('property');
  }

  public function targets() {
    return self::PROPERTY_CONSTRAINT;
  }

}