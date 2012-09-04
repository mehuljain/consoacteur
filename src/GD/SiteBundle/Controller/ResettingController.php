<?php

namespace GD\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use GD\AdminBundle\Entity\User;
use GD\SiteBundle\Form\Type\PasswordResetRequestType;
use GD\SiteBundle\Form\Type\ForgotPasswordType;
use FOS\UserBundle\Model\UserInterface;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class ResettingController extends Controller {

  /**
   * This action requests the user for his/her username/email to reset the password and sends the
   * reset link to his/her email address.
   * 
   * @Route("/resetting/", name="gd_site_user_resetting")   
   * @Template()
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  public function indexAction(Request $request, $isAjax = false) {
    
    // Redirect logged in users to homepage when accessing this url
    if ($this->get('security.context')->isGranted('ROLE_USER'))
    {
        // redirect authenticated users to homepage
        return new RedirectResponse($this->container->get('router')->generate('gd_top_merchants'));
    }

    $breadcrumbs = $this->get("white_october_breadcrumbs");
    $breadcrumbs->addItem($this->get('translator')->trans('home.page', array(), 'breadcrumb'), $this->get("router")->generate("gd_home"));
    $breadcrumbs->addItem($this->get('translator')->trans('forgot.password', array(), 'breadcrumb'), $this->get("router")->generate("gd_site_user_resetting"));

    $form = $this->createForm(new PasswordResetRequestType());

    if ('POST' == $request->getMethod()) {
      
      $form->bindRequest($request);
      $postData = $request->request->get('gd_user_password_reset_request');
      $usernameOrEmail = $postData['usernameOrEmail'];

      $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($usernameOrEmail);

      if (null === $user) {
        $this->get('session')->setFlash('error', $this->get('translator')->trans('passwordreset.invaliduser.message',array('%usernameOrEmail%' => $usernameOrEmail), 'flashmessages'));
        return $this->redirect($this->generateUrl('gd_site_user_resetting'));
      }

      if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {

        $resetHourTime = bcdiv($this->container->getParameter('fos_user.resetting.token_ttl'),3600,0);
        $this->get('session')->setFlash('error', $this->get('translator')->trans('passwordreset.repeatrequest.message',array('%resetHourTime%' => $resetHourTime), 'flashmessages'));
        return $this->redirect($this->generateUrl('gd_site_user_resetting'));
      }
      
      //Do not allow password reset for blacklisted, archived,admin and locked accounts
      if(!\GD\SiteBundle\Controller\SecurityController::checkActivation($user)) {
        $this->get('session')->setFlash('error', $this->get('translator')->trans('passwordreset.unauthorizeduser.message',array(), 'flashmessages'));
        return $this->redirect($this->generateUrl('gd_site_user_resetting'));
      }

      if ($form->isValid()) {
        
        $user->generateConfirmationToken();
        if ($this->sendResettingEmailMessage($user)) {
          $this->get('session')->setFlash('success', $this->get('translator')->trans('passwordreset.mailsent.message',array(), 'flashmessages'));
        } else {
          $this->get('session')->setFlash('error', $this->get('translator')->trans('passwordreset.mailsenderror.message',array(), 'flashmessages'));
        }
        
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return $this->render('GDSiteBundle:Resetting:resetting.html.twig', array('form' => $form->createView()));
      }
    }
    
     if ($this->getRequest()->isXmlHttpRequest() || $isAjax) {
      return $this->render('GDSiteBundle:Resetting:resetting_ajax.html.twig', array('form' => $form->createView()));
    }

    return $this->render('GDSiteBundle:Resetting:resetting.html.twig', array('form' => $form->createView()));
  }

  /**
   * Send the resetting email message 
   * 
   * @param UserInterface object 
   */
  public function sendResettingEmailMessage(UserInterface $user) {
    
    try{
      $url = $this->get('router')->generate('gd_site_user_resetting_confirm', array('confirmationToken' => $user->getConfirmationToken()), true);
      $renderTemplate = 'GDSiteBundle:Mail:resetEmail.html.twig';
      $context = array('user' => $user,'resetUrl' => $url);

      $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
      $this->get('mailer_utility')->sendEmailMessage($renderTemplate,$context, $fromEmail, $user->getEmail());
       return true;
    }
    catch(\Exception $e){
      return false;
    }
    
  }  

  /**
   *  When the user clicks on the reset link sent to his/her email address, this action is called.
   * 
   * @Route("/reset/password/{confirmationToken}", name="gd_site_user_resetting_confirm")
   * @PreAuthorize("isAnonymous()") 
   * @param type $confirmationToken
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   * @throws NotFoundHttpException /
   */
  public function resetAction(Request $request, $confirmationToken) {
    
   $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($confirmationToken);

    if (null === $user) {
      throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $confirmationToken));
    }
    
    if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {

        $this->get('session')->setFlash('error', $this->get('translator')->trans('passwordreset.linkexpired.message',array(), 'flashmessages'));
        return $this->redirect($this->generateUrl('gd_site_user_resetting'));
    }

    $form = $this->createForm(new ForgotPasswordType($this->get('translator')), $user);

    if ('POST' == $request->getMethod()) {
      
      $form->bindRequest($request);
      
      if ($form->isValid()) {
       
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
        $user->setPassword($password);
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
        $this->authenticateUser($user);
        $em = $this->get('doctrine')->getEntityManager();
        $em->persist($user);
        $em->flush();
        $this->getRequest()->getSession()->setFlash('profileUpdate', $this->get('translator')->trans('passwordupdate.success.message',array(), 'flashmessages'));
        return $this->redirect($this->getRedirectionUrl($user));
      }
    }
    return $this->render('GDSiteBundle:Resetting:password_resets.html.twig', array('form' => $form->createView()));
  }
  
  /**
   * Authenticate users
   * 
   * @param UserInterface $user
   * @return boolean 
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
   * Generate the redirection url when the resetting is completed.
   *
   * @param \FOS\UserBundle\Model\UserInterface $user
   *
   * @return string
   */
  protected function getRedirectionUrl(User $user) {
    if($user->getIsOlduser())
    {
      return $this->container->get('router')->generate('gd_site_legal_confirm'); ;
    }
    return $this->container->get('router')->generate('fos_user_profile_show');
  }

}