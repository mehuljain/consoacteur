<?php

namespace GD\AdminBundle\Form\Model;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Validator;
/**
 * @Validator\Password(message="user.plainPassword.not_valid",userProperty="user",passwordProperty="current",groups={"admin_profile"}) 
 */
class CheckPassword 
{
    /**
     * User whose password is changed
     *
     * @var UserInterface
     */
    public $user;

    /**
     * @var string
     * 
     * 
     * 
     */
    public $current;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }
}
