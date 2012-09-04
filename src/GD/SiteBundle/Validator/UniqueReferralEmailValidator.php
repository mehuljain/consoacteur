<?php

namespace GD\SiteBundle\Validator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\Container;

class UniqueReferralEmailValidator extends ConstraintValidator {

  private $entityManager;
  private $container;

  public function __construct(EntityManager $entityManager,Container $container) {
    $this->entityManager = $entityManager;
    $this->container = $container;
  }

  public function isValid($value,Constraint $constraint) {
    $id = $this->container->get('security.context')->getToken()->getUser()->getId();
    // try to get one entity that matches the constraint
    $userCount = $this->entityManager->getRepository('GDAdminBundle:Referral')
            ->findOneBy(array($constraint->property => $value,'user' => $id));
    
    if (!empty($userCount)) {
       // the constraint does not pass
      $this->setMessage($constraint->message);
      return false;
      
    } else {
      // the constraint passes
      return true;
    }
  }
}
