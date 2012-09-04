<?php

namespace GD\SiteBundle\Validator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckSponsorValidator extends ConstraintValidator {

  private $entityManager;

  public function __construct(EntityManager $entityManager) {
    $this->entityManager = $entityManager;
  }

  public function isValid($value, Constraint $constraint) {
    // try to get one entity that matches the constraint
    $sponsor = $this->entityManager->getRepository('GDAdminBundle:User')
            ->findOneBy(array($constraint->property => $value));
    // if there is a username then ok...
    if ($sponsor !== null || empty($value)) {
      // the constraint passes
      return true;
    } else {
      // the constraint does not pass
      $this->setMessage($constraint->message);
      return false;
    }
  }
}
