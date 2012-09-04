<?php

namespace GD\SiteBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use GD\SiteBundle\Form\Model\ChangePassword;

class CheckPasswordFormHandler
{
    protected $request;
    protected $userManager;
    protected $form;

    public function __construct(Form $form, Request $request, UserManagerInterface $userManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
    }

   
    public function process(UserInterface $user)
    {
        
        if ('POST' === $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
               
                return true;
            }
        }

        return false;
    }

}
