<?php

namespace GD\AdminBundle\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;
use GD\AdminBundle\Form\Model\CheckPassword;

class ChangeEmail extends CheckPassword
{

/**
  * @var string
  * 
  * @Assert\Email(message = "user.email.not_valid",groups={"admin_profile"})
  * @Assert\NotBlank(message = "user.email.not_blank", groups={"admin_profile"})
  */
 public $new;   
   
 }
