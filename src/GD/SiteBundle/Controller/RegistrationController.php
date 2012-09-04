<?php

namespace GD\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GD\AdminBundle\Entity\User;
use GD\SiteBundle\Form\Type\RegistrationType;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RegistrationController extends Controller {

  const SESSION_REDIRECTION = '';
  
  /**
   *  This action handles the registration of the frontend users.
   * 
   * @Route("/register", name="gd_site_user_registration")
   * @Template()
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param bool $isAjax
   * @return array
   */
  public function registerAction(Request $request, $isAjax = false,  $merchantRedirection = false) {
  
   // Redirect logged in users to homepage when accessing this url
    if ($this->get('security.context')->isGranted('ROLE_USER'))
    {
        // redirect authenticated users to homepage
        return new RedirectResponse($this->container->get('router')->generate('gd_top_merchants'));
    }
    
    $errors = 1;
    if (!$isAjax) {
      $session = $this->get('Session');
      $session->set('menu', 'register'); //To prevent other menus from highlighting
    }

    $user = new User();
    $form = $this->createForm(new RegistrationType($this->get('translator')), $user);
    $form->setData($user);

    $em = $this->getDoctrine()->getEntityManager();
    $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
    $request = $this->get('request');

    if ('POST' == $request->getMethod()) {
      $form->bindRequest($request);

      $factory = $this->container->get('security.encoder_factory');
      $encoder = $factory->getEncoder($user);
      //where $user->plainPassword has been bound in plaintext by the form
      $password = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
      $user->setPassword($password);
      $merchantRedirection = ($request->request->get('merchantRedirection')=== NULL) ? 'FALSE' : 'TRUE';

      if ($form->isValid()) {
        $user = $form->getData();
        if ($confirmationEnabled) {
          //If confirmation email is required
          $user->setEnabled(false);
          //Send confirmation email to the user
          $this->sendConfirmationEmailMessage($user);
          $route = 'gd_site_user_registration_check_mail';
          } else {
          //ok no confirmation email is required
          $user->setConfirmationToken(null);
          $user->setEnabled(true);
          $this->authenticateUser($user);
          $route = 'gd_site_user_registration_confirmed';
        }
        
        $em->persist($user);
        $em->flush();
        if($merchantRedirection == 'TRUE'){
          /** To Manage the user redirection condition**/
          $this->container->get('session')->set(static::SESSION_REDIRECTION, $user->getId());
        }
        $url = $this->generateUrl($route,array('merchantRedirection' => $merchantRedirection));

        return $this->redirect($url);
      }
      else {
        // Form is not valid, therefore set the error flag for ajax forms redirection
        $errors = 2;
      }
    }

    if ($this->getRequest()->isXmlHttpRequest() || $isAjax) {
       
      if ($merchantRedirection == 'TRUE') {         
        return $this->render('GDSiteBundle:Registration:register_redirection.html.twig', array('form' => $form->createView(), 'error' => $errors));
      } else {
        return $this->render('GDSiteBundle:Registration:register_ajax.html.twig', array('form' => $form->createView(), 'error' => $errors));
      }
    }

    return $this->render('GDSiteBundle:Registration:register.html.twig', array('form' => $form->createView()));
  }

  /**
   *  This function renders the success messages and requests the user to confirm by clicking 
   *  on the registration click sent to their email address.
   * 
   * @Route("/register/checkmail/{merchantRedirection}", name="gd_site_user_registration_check_mail")   * 
   * 
   * @return array
   */
  public function registerCheckEmailAction(Request $request) {
    
    // Redirect logged in users to homepage when accessing this url
    if ($this->get('security.context')->isGranted('ROLE_USER'))
    {
        // redirect authenticated users to homepage
        return new RedirectResponse($this->container->get('router')->generate('gd_top_merchants'));
    }
    
    if ($this->getRequest()->isXmlHttpRequest()) {  
       $merchantRedirection = $request->get('merchantRedirection');
       if($merchantRedirection == 'TRUE'){
         $userId = $this->container->get('session')->get(static::SESSION_REDIRECTION);
         $this->container->get('session')->remove(static::SESSION_REDIRECTION);
         
         return $this->container->get('templating')->renderResponse('GDSiteBundle:Registration:registerRedirection_ajax_success.html.twig',array('userId' => $userId));
       }
      return $this->container->get('templating')->renderResponse('GDSiteBundle:Registration:register_ajax_success.html.twig');
    }
    return $this->container->get('templating')->renderResponse('GDSiteBundle:Registration:success.html.twig');
  }

  /**
   *  This action is called when the user clicks the registration link sent to his/her email address.
   * 
   * @Route("/register/confirm/{confirmationToken}", name="gd_site_user_registration_confirm_email")
   *  
   * @param \GD\AdminBundle\Entity\User $user
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function confirmViaEmailAction($confirmationToken) {
    
    $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($confirmationToken);
    
    if (null === $user) {
      if($this->container->get('kernel')->getEnvironment() == 'dev') {
            throw new NotFoundHttpException(sprintf('Sorry. This is not a valid registration Url'));
      }
      return $this->redirect($this->generateUrl('gd_home'));
    }
    $user->setConfirmationToken(null);
    $user->setEnabled(true);
    $user->setLastLogin(new \DateTime());

    $this->getDoctrine()->getEntityManager()->persist($user);
    $this->getDoctrine()->getEntityManager()->flush();
    $this->authenticateUser($user);

    return $this->redirect($this->generateUrl('gd_site_user_registration_confirmed'));
  }

  /**
   *  The user when confirmed and authenticated is redirected to the profile page through this controller action. 
   * 
   * @Route("/register/confirmed", name="gd_site_user_registration_confirmed")
   * 
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function confirmedAction() {

    $this->getRequest()->getSession()->setFlash('registration_successmessage',$this->get('translator')->trans('registration.success.message',array(), 'flashmessages'));
   
    $user = $this->container->get('security.context')->getToken()->getUser();
    if (!is_object($user) || !$user instanceof UserInterface) {
      throw new AccessDeniedException('This user does not have access to this section.');
    }
    return new RedirectResponse($this->generateUrl('gd_site_user_profile'));
  }

  /**
   * Authenticate a user with FOS Security
   * 
   * @param FOS\UserBundle\Model\UserInterface
   */
  protected function authenticateUser(UserInterface $user) {
    try {
      $this->container->get('fos_user.user_checker')->checkPostAuth($user);
    } catch (AccountStatusException $e) {
      // Don't authenticate locked, disabled or expired users
      return;
    }

    $providerKey = $this->container->getParameter('fos_user.firewall_name');
    $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
    $this->container->get('security.context')->setToken($token);
  }
  
  /**
   * Send the confirmation email message 
   * 
   * @param UserInterface object 
   */
  public function sendConfirmationEmailMessage(UserInterface $user) {
    $url = $this->get('router')->generate('gd_site_user_registration_confirm_email', array('confirmationToken' => $user->getConfirmationToken()), true);
    $renderTemplate = 'GDSiteBundle:Mail:confirmEmail.html.twig';
    $context = array('user' => $user,'confirmationUrl' => $url);

    $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
    $this->get('mailer_utility')->sendEmailMessage($renderTemplate,$context, $fromEmail, $user->getEmail());
  }
  
}
