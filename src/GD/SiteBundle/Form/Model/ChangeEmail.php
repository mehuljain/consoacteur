<?php

namespace GD\SiteBundle\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeEmail extends CheckPassword
{

/**
  * @var string
  * 
  * @Assert\Email(message = "user.email.not_valid",groups={"profile"})
  * @Assert\NotBlank(message = "user.email.not_blank", groups={"profile"})
  */
 public $new;   
   
 }
