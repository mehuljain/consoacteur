<?php

namespace GD\AdminBundle\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword extends CheckPassword
{
 /**
  * @var string
  * 
  * @Assert\MinLength(
  *     message="user.plainPassword.not_length",
  *     limit=4,groups={"admin_profile"})
  * @Assert\NotNull(message= "user.plainPassword.not_blank",groups={"admin_profile"})
  */
    public $new;
}
