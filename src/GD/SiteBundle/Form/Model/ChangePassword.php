<?php

namespace GD\SiteBundle\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword extends CheckPassword
{
 /**
  * @var string
  * 
  * @Assert\MinLength(
  *     message="user.plainPassword.not_length",
  *     limit=4,groups={"profile"})
  * @Assert\NotNull(message= "user.plainPassword.not_blank",groups={"profile"})
  */
    public $new;
}
