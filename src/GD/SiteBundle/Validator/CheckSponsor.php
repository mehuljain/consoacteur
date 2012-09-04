<?php

namespace GD\SiteBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation 
 */
class CheckSponsor extends Constraint {

  public $message = 'Invalid sponsor code. Please enter the correct one';
  public $property;

  public function validatedBy() {
    return 'validator.sponsor';
  }

  public function requiredOptions() {
    return array('property');
  }

  public function targets() {
    return self::PROPERTY_CONSTRAINT;
  }

}