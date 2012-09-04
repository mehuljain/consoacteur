<?php

namespace GD\AdminBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use GD\AdminBundle\Form\Model\CheckPassword;
use GD\AdminBundle\Form\Type\ChangeEmailType;
use GD\AdminBundle\Form\Model\ChangeEmail;
use FOS\UserBundle\Validator\Unique;

class ChangeEmailFormHandler
{
    protected $request;
    protected $userManager;
    protected $form;
    protected $translator;

    public function __construct(Form $form, Request $request, UserManagerInterface $userManager, \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
        $this->translator = $translator;
    }

    public function getNewEmail()
    {
      
        return $this->form->getData()->new;
    }

    public function process(UserInterface $user)
    {
        $this->form->setData(new ChangeEmail($user));

        if ('POST' === $this->request->getMethod()) {
            $this->form->bindRequest($this->request);
            
            $email = $this->getNewEmail();
            $existUser = $this->userManager->findUserByEmail($email);            
            
            if(!empty($existUser)){
              $this->form->addError(new FormError($this->translator->trans('user.email.not_unique',array(), 'validators'),array('property' => 'new')));
              return false;
            }
            
            if ($this->form->isValid()) {
               
                $this->onSuccess($user);

                return true;
            }
        }

        return false;
    }

    protected function onSuccess(UserInterface $user)
    {
        $user->setEmail($this->getNewEmail());
        $this->userManager->updateUser($user);
    }
}
