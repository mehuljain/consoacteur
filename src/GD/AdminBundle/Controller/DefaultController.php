<?php

namespace GD\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GD\AdminBundle\Form\Model\ChangePassword;
use GD\AdminBundle\Form\Model\ChangeEmail;
use GD\AdminBundle\Form\Handler\ChangePasswordFormHandler;
use GD\AdminBundle\Form\Handler\ChangeEmailFormHandler;
use GD\AdminBundle\Form\Type\ChangeEmailType;
use GD\AdminBundle\Form\Type\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request as httpRequest;

class DefaultController extends Controller
{
    /**
     * @Route("/switch_locale/{locale}", name="switch_locale")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * Change the current locale
     */
    public function switchLocaleAction($locale, \Symfony\Component\HttpFoundation\Request $request)
    {
        $session = $request->getSession();

        $session->setLocale($locale);
        $last_route = $session->get('last_route', array('name' => 'index'));
        $last_route['params']['_locale'] = $locale;

        return ($this->redirect($this->generateUrl($last_route['name'], $last_route['params'])));
    }

    public function localeAction()
    {
        $languages = $this->container->getParameter('languages');
        return $this->render('GDAdminBundle::locale.html.twig', array('languages' => $languages));
    }

    /**
     * @Route("/switch_base_language/{language}", name="switch_base_language")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * Change the base languague in the backend. The SonataAdmin translated fields are rendered according 
     * to the BaseLanguage locale, as comapred to the Request->Session's locale. Corresponding trans() and
     * transChoice() methods are overridden in SonataAdmin class
          
    public function switchBaseLanguageAction($language, \Symfony\Component\HttpFoundation\Request $request)
    {
        $session = $request->getSession();

        $session->set('base_language', $language);
        //$this->get('translator')->setLocale($language);
        $last_route = $session->get('last_route', array('name' => 'index'));

        return ($this->redirect($this->generateUrl($last_route['name'], $last_route['params'])));
    }

    public function baseLanguageAction()
    {
        $languages = $this->container->getParameter('languages');
        return $this->render('GDAdminBundle::language.html.twig', array('languages' => $languages));
    }
    */
    
    /*
     *  Allows a backend user to change email. 
     */
    public function changeEmailAction(httpRequest $request) {

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($this->get('security.context')->getToken()->getUser()->getUsernameCanonical());
        $translator = $this->get('translator');
        $changeEmail = new ChangeEmail($user);
        
        $form = $this->createForm(new ChangeEmailType($translator), $changeEmail);
        $formHandler = new ChangeEmailFormHandler($form, $request, $userManager,$translator);

        $process = $formHandler->process($user);

        if ($process) {
          $this->get('session')->setFlash('sonata_flash_success', $this->get('translator')->trans('emailupdate.success.message',array(), 'flashmessages'));
          return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
        }

        return $this->render('GDAdminBundle:Default:changeEmail.html.twig', array('form' => $form->createView()));
    }

    /*
     *  Allows a backend user to change password.
     */
    public function changePasswordAction(httpRequest $request) {

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($this->get('security.context')->getToken()->getUser()->getUsernameCanonical());

        $changePassword = new ChangePassword($user);
        $form = $this->createForm(new ChangePasswordType($this->get('translator')), $changePassword);
        $formHandler = new ChangePasswordFormHandler($form, $request, $userManager);
        $process = $formHandler->process($user);

        if ($process) {
          $this->get('session')->setFlash('sonata_flash_success', $this->get('translator')->trans('passwordupdate.success.message',array(), 'flashmessages'));
          return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
        }
        return $this->render('GDAdminBundle:Default:changePassword.html.twig', array('form' => $form->createView()));
    }
}
