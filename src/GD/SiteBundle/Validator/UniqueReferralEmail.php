<?php

namespace GD\SiteBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation 
 */
class UniqueReferralEmail extends Constraint {

  public $message = 'You have already sent a referral email to this address';
  public $property;

  public function validatedBy() {
    return 'validator.uniquereferralemail';
  }
  
  public function requiredOptions() {
    return array('property');
  }

  public function targets() {
    return self::PROPERTY_CONSTRAINT;
  }

}