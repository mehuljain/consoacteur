<?php

namespace GD\SiteBundle\Validator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator {

  private $entityManager;

  public function __construct(EntityManager $entityManager) {
    $this->entityManager = $entityManager;
  }

  public function isValid($value,Constraint $constraint) {
    // try to get one entity that matches the constraint
    $emailCount = $this->entityManager->getRepository('GDAdminBundle:User')
            ->findOneBy(array($constraint->property => $value));
    
    if (!empty($emailCount)) {
       // the constraint does not pass
      $this->setMessage($constraint->message);
      return false;
      
    } else {
      // the constraint passes
      return true;
    }
  }
}
