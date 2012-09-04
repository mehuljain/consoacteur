<?php

namespace GD\AdminBundle\Controller;

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
use GD\AdminBundle\Form\Type\PasswordResetRequestType;
use GD\AdminBundle\Form\Type\ForgotPasswordType;
use FOS\UserBundle\Model\UserInterface;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class ResettingController extends Controller {

  const SESSION_EMAIL = 'fos_user_send_resetting_email/email';

  /**
   * Initiate reset password by sending a link to the User's registered email. 
   *
   * @Route("/admin/resetting", name="gd_admin_user_resetting")
   * @PreAuthorize("isAnonymous()") 
   * @Template()
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  public function indexAction(Request $request, $isAjax = false, $source = null) {

    $form = $this->createForm(new PasswordResetRequestType());

    if ('POST' == $request->getMethod()) {
      
      $form->bindRequest($request);
      $postData = $request->request->get('gd_user_password_reset_request');
      $usernameOrEmail = $postData['usernameOrEmail'];

      $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($usernameOrEmail);

      if (null === $user) {
        $this->get('session')->setFlash('error', 'The Username or Email you entered does not exist.');
        return $this->redirect($this->generateUrl('gd_admin_user_resetting'));
      }

      if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {

        $this->get('session')->setFlash('success', 'You have already made a request to reset password. Please check your mail for Password reset link. ');
        return $this->redirect($this->generateUrl('gd_admin_user_resetting'));
      }

      if ($form->isValid()) {
        
        $user->generateConfirmationToken();
        if ($this->sendResettingEmailMessage($user)) {
          $this->get('session')->setFlash('success', 'An Email has been sent to your mail-id. Please click on the password reset link.');
        } else {
          $this->get('session')->setFlash('error', 'Something went wrong! We are sorry for the inconvenience. Please try again later.');
        }
        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return $this->render('GDAdminBundle:Resetting:resetting.html.twig', array('form' => $form->createView()));
      }
    }

    return $this->render('GDAdminBundle:Resetting:resetting.html.twig', array('form' => $form->createView()));
  }

  /**
   * Send the resetting email message 
   * 
   * @param UserInterface object 
   */
  public function sendResettingEmailMessage(UserInterface $user) {
    
    try{
      $url = $this->get('router')->generate('gd_admin_user_resetting_confirm', array('confirmationToken' => $user->getConfirmationToken()), true);
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
   * Reset the password after the user clicks the link received in email.
   *
   * @Route("admin/reset/password/{confirmationToken}", name="gd_admin_user_resetting_confirm")
   * @param type $confirmationToken
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   * @throws NotFoundHttpException /
   */
  public function resetAction(Request $request, $confirmationToken) {
    
    $session = $this->container->get('session');
    $session->remove(static::SESSION_EMAIL);
    $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($confirmationToken);

    if (null === $user) {
      throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $confirmationToken));
    }
    
    if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {

        $this->get('session')->setFlash('error', 'Your request link has expired. Please make another request by entering username or email');
        return $this->redirect($this->generateUrl('gd_admin_user_resetting'));
    }

    $form = $this->createForm(new ForgotPasswordType(), $user);

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
        $this->getRequest()->getSession()->setFlash('message', 'Your password has been reset successfully!');
        return new RedirectResponse($this->getRedirectionUrl($user));
      }
    }
    return $this->render('GDAdminBundle:Resetting:password_resets.html.twig', array('form' => $form->createView()));
  }

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
  protected function getRedirectionUrl(UserInterface $user) {
    return $this->container->get('router')->generate('sonata_admin_dashboard');
  }

  /**
   * Get the truncated email displayed when requesting the resetting.
   *
   * The default implementation only keeps the part following @ in the address.
   *
   * @param \FOS\UserBundle\Model\UserInterface $user
   *
   * @return string
   */
  protected function getObfuscatedEmail(UserInterface $user) {
    $email = $user->getEmail();
    if (false !== $pos = strpos($email, '@')) {
      $email = '...' . substr($email, $pos);
    }

    return $email;
  }

}
