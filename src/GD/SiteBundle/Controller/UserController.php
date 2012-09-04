<?php

namespace GD\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request as httpRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GD\AdminBundle\Entity\User;
use GD\AdminBundle\Entity\Request;
use GD\AdminBundle\Entity\Withdrawals;
use GD\AdminBundle\Entity\Referral;
use GD\AdminBundle\Entity\Merchant;
use GD\AdminBundle\Entity\Transaction;
use GD\SiteBundle\Form\Type\ProfileType;
use GD\SiteBundle\Form\Type\ChangeEmailType;
use GD\SiteBundle\Form\Type\PasswordResetType;
use GD\SiteBundle\Form\Type\LoginType;
use GD\SiteBundle\Form\Type\ReferFriendType;
use GD\SiteBundle\Form\Type\NewRequestType;
use GD\SiteBundle\Form\Type\AccountClosureType;
use GD\SiteBundle\Form\Type\ConfirmLegalType;
use GD\SiteBundle\Form\Model\ChangePassword;
use GD\SiteBundle\Form\Model\ChangeEmail;
use GD\SiteBundle\Form\Handler\ChangePasswordFormHandler;
use GD\SiteBundle\Form\Handler\ChangeEmailFormHandler;

/**
 * @Route("/secured")
 */
class UserController extends Controller {

  const TYPE_CHEQUE = 1;
  const TYPE_BANK1 = 2;
  const TYPE_BANK2 = 3;
  const TYPE_PAYPAL = 4;
  const BC_SCALE_PRECISION = 2;
  
  const SESSION_CLOSURE = '';

  /**
   * This function calculates the various amounts(offeramount, referral amount...) for the user.
   * 
   * @param type $user
   * @return type 
   */
  public static function getAccountSummary($user) {
    $withdrawableAmount = 0;
    $confirmedAndValidatedAmount = 0;
    $referralAmountToBeValidated = 0;
    $offerAmountToBeValidated = 0;
    $requestedAmount = 0;
    $withdrawnAmount = 0;
    $invalidatedAmount = 0;
    $onHoldAmount = 0;

    $transactions = $user->getTransactions();
    foreach ($transactions as $transaction) {
      $amount = $transaction->getUserGainAmount();

      switch ($transaction->getStatus()) {
        case Transaction::TRANSACTION_STATUS_WAITING:
          if (Transaction::TRANSACTION_TYPE_REFERRAL === $transaction->getType()) {
            $referralAmountToBeValidated = bcadd($referralAmountToBeValidated, $amount, self::BC_SCALE_PRECISION);
          } elseif (Transaction::TRANSACTION_TYPE_OFFER === $transaction->getType()) {
            $offerAmountToBeValidated = bcadd($offerAmountToBeValidated, $amount,self::BC_SCALE_PRECISION);
          }
          break;
        case Transaction::TRANSACTION_STATUS_PENDING_CONFIRMATION:
          if (Transaction::TRANSACTION_TYPE_REFERRAL === $transaction->getType()) {
            $referralAmountToBeValidated = bcadd($referralAmountToBeValidated, $amount, self::BC_SCALE_PRECISION);
          } elseif (Transaction::TRANSACTION_TYPE_OFFER === $transaction->getType()) {
            $offerAmountToBeValidated = bcadd($offerAmountToBeValidated, $amount,self::BC_SCALE_PRECISION);
          }
          break;
        case Transaction::TRANSACTION_STATUS_CONFIRMED:
          $withdrawableAmount = bcadd($withdrawableAmount, $transaction->getUserGainAmount(), self::BC_SCALE_PRECISION);
          $confirmedAndValidatedAmount = bcadd($confirmedAndValidatedAmount, $amount, self::BC_SCALE_PRECISION);
          break;
        case Transaction::TRANSACTION_STATUS_PAYMENT_REQUESTED:
          $requestedAmount = bcadd($requestedAmount, $amount, self::BC_SCALE_PRECISION);
          break;
        case Transaction::TRANSACTION_STATUS_APPROVED:
          $requestedAmount = bcadd($requestedAmount, $amount, self::BC_SCALE_PRECISION);
          break;
        case Transaction::TRANSACTION_STATUS_PAID:
          $withdrawnAmount = bcadd($withdrawnAmount, $amount, self::BC_SCALE_PRECISION);
          break;
        case Transaction::TRANSACTION_STATUS_CANCELLED:
          $invalidatedAmount = bcadd($invalidatedAmount, $amount, self::BC_SCALE_PRECISION);
          break;
        case Transaction::TRANSACTION_STATUS_REJECTED:
          $invalidatedAmount = bcadd($invalidatedAmount, $amount, self::BC_SCALE_PRECISION);
          break;
        case Transaction::TRANSACTION_STATUS_ON_HOLD:
          $onHoldAmount = bcadd($onHoldAmount, $amount, self::BC_SCALE_PRECISION);
          break;
        case Transaction::TRANSACTION_STATUS_LOST:
          $invalidatedAmount = bcadd($invalidatedAmount, $amount, self::BC_SCALE_PRECISION);
          break;
      }
    }
    return array('withdrawableAmount' => $withdrawableAmount,
                 'confirmedAndValidatedAmount' => $confirmedAndValidatedAmount,
                 'referralAmountToBeValidated' => $referralAmountToBeValidated,
                 'offerAmountToBeValidated' => $offerAmountToBeValidated,
                 'requestedAmount' => $requestedAmount,
                 'withdrawnAmount' => $withdrawnAmount,
                 'invalidatedAmount' => $invalidatedAmount,
                 'onHoldAmount' => $onHoldAmount);
    
  }
  
  /**
   * Generic function to create the BreadCrumbs for the User Controller pages.
   *
   * @param string $linkName
   * @param string $routeName 
   * @param string $topRouteName
   * @param string $topLinkName
   */
  private function createBreadcrumbs($linkName, $routeName, $topRouteName = NULL , $topLinkName = NULL)
  {
    $breadcrumbs = $this->get("white_october_breadcrumbs");
    $breadcrumbs->addItem($this->get('translator')->trans('home.page', array(), 'breadcrumb'), $this->get("router")->generate("gd_home"));
    if(!empty($topLinkName)){
      $breadcrumbs->addItem($this->get('translator')->trans($topLinkName, array(), 'breadcrumb'), $this->get("router")->generate($topRouteName));
    }
    $breadcrumbs->addItem($this->get('translator')->trans($linkName, array(), 'breadcrumb'), $this->get("router")->generate($routeName));
    
  }
  
  /**
   *  Updates made to the user profile page are controlled using this action.
   * 
   * @Route("/profile", name="gd_site_user_profile")
   * @Template()
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function profileAction(httpRequest $request) {

    $this->createBreadcrumbs('your.profile',$request->get('_route'));
    $em = $this->getDoctrine()->getEntityManager();
    $user = $em->getRepository('GDAdminBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
    $form = $this->createForm(new ProfileType(), $user);

    if ($request->getMethod() == 'POST') {

      $form->bindRequest($request);

      if ($form->isValid()) {

        $em->persist($user);
        $em->flush();

        $this->get('session')->setFlash('profileUpdate', $this->get('translator')->trans('profileupdate.success.message',array(), 'flashmessages'));
        return $this->redirect($this->generateUrl('gd_site_user_profile'));
      }      
    }
    return $this->render('GDSiteBundle:User:viewProfile.html.twig', array('form' => $form->createView()));
  }

  /**
   * The change of frontend user email is managed through this action.
   * 
   * @Route("/change-email", name="gd_site_user_change_email")
   * @Template()
   */
  public function changeEmailAction(httpRequest $request) {

    $this->createBreadcrumbs('change.email',$request->get('_route'),'gd_site_user_profile','your.profile');
    $userManager = $this->get('fos_user.user_manager');
    $user = $userManager->findUserByUsername($this->get('security.context')->getToken()->getUser()->getUsernameCanonical());
    $translator = $this->get('translator');
    $changeEmail = new ChangeEmail($user);
    $form = $this->createForm(new ChangeEmailType($translator), $changeEmail);
    $formHandler = new ChangeEmailFormHandler($form, $request, $userManager,$translator);

    $process = $formHandler->process($user);

    if ($process) {
      
      $this->get('session')->setFlash('profileUpdate', $this->get('translator')->trans('emailupdate.success.message',array(), 'flashmessages'));
      return $this->redirect($this->generateUrl('gd_site_user_profile'));
    }

    return $this->render('GDSiteBundle:User:changeEmail.html.twig', array('form' => $form->createView()));
  }

  /**
   * The change of frontend user password is managed through this action.
   * 
   * @Route("/change-password", name="gd_user_change_password")
   * @Template()
   */
  public function changePasswordAction(httpRequest $request) {

    $this->createBreadcrumbs('change.password',$request->get('_route'),'gd_site_user_profile','your.profile');
    $userManager = $this->get('fos_user.user_manager');
    $user = $userManager->findUserByUsername($this->get('security.context')->getToken()->getUser()->getUsernameCanonical());

    $changePassword = new ChangePassword($user);
    $form = $this->createForm(new \GD\SiteBundle\Form\Type\ChangePasswordType($this->get('translator')), $changePassword);
    $formHandler = new ChangePasswordFormHandler($form, $request, $userManager);
    $process = $formHandler->process($user);

    if ($process) {
      
      $this->get('session')->setFlash('profileUpdate', $this->get('translator')->trans('passwordupdate.success.message',array(), 'flashmessages'));
      return $this->redirect($this->generateUrl('gd_site_user_profile'));
    }
    return $this->render('GDSiteBundle:User:changePassword.html.twig', array('form' => $form->createView()));
  }

  /**
   * The user password validation method before redirecting him/her to account closure page.
   * 
   * @Route("/validate/password", name="gd_validate_password")
   * @Template()
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function passwordValidateAction(httpRequest $request) {
    
    $this->createBreadcrumbs('check.password',$request->get('_route'),'gd_site_user_profile','your.profile');
    $userManager = $this->get('fos_user.user_manager');
    $user = $userManager->findUserByUsername($this->get('security.context')->getToken()->getUser()->getUsernameCanonical());

    $checkPassword = new \GD\SiteBundle\Form\Model\CheckPassword($user);
    $form = $this->createForm(new \GD\SiteBundle\Form\Type\CheckPasswordType(), $checkPassword);
    
    $formHandler = new \GD\SiteBundle\Form\Handler\CheckPasswordFormHandler($form, $request, $userManager);
    
    $process = $formHandler->process($user);
    if ($process) {
         
        $this->container->get('session')->set(static::SESSION_CLOSURE, 'close-account');
        return $this->redirect($this->generateUrl('gd_site_user_account_closure'));
    }   

    return $this->render('GDSiteBundle:User:checkPassword.html.twig', array('form' => $form->createView()));
  }

  /**
   * User account closure form validation.
   * @Route("/close-account", name="gd_site_user_account_closure")
   * @Template()
   *
   * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
   */
  public function userAccountClosureAction(httpRequest $request) {
    
      $session = $this->container->get('session');
      $redirectAction = $session->get(static::SESSION_CLOSURE);
      
       if ($redirectAction != 'close-account') {
            // the user does not come from the check password page
            return $this->redirect($this->container->get('router')->generate('gd_site_user_profile'));
        }
  
    $this->createBreadcrumbs('account.closure',$request->get('_route'),'gd_site_user_profile','your.profile');

    $form = $this->get('form.factory')->create(new AccountClosureType());

    if ($request->getMethod() == 'POST') {
      $form->bindRequest($request);

      if ($form->isValid()) {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('GDAdminBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

        if (!$user) {
          throw $this->createNotFoundException('User not found');
        }
        $user->setIsArchived(1);
        $user->setEnabled(0);
        $em->flush();
        $session->remove(static::SESSION_CLOSURE);
        $this->sendAccountClosureEmailMessage($user);
        return $this->redirect($this->generateUrl('fos_user_security_logout'));
      }
    }

    return $this->render('GDSiteBundle:User:accountClosure.html.twig', array('form' => $form->createView()));
  }
  
  /**
   * The function to send an email confirming the closure of user's account.
   * 
   * @param User $user
   * @return boolean 
   */
  
  public function sendAccountClosureEmailMessage(User $user) {
    
    try{      
      $renderTemplate = 'GDSiteBundle:Mail:accountClosureEmail.html.twig';
      $context = array('user' => $user);

      $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
      $this->get('mailer_utility')->sendEmailMessage($renderTemplate,$context, $fromEmail, $user->getEmail());
       return true;
    }
    catch(\Exception $e){
      return false;
    }    
  }  

  /** 
   * Renders the user earning details page.
   * 
   * @Route("/income-detail", name="gd_site_user_earnings_detail")
   * @Template()
   *
   * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
   */
  public function userEarningsDetailAction(httpRequest $request) {
    $this->createBreadcrumbs('your.earnings.details',$request->get('_route'),'gd_site_withdrawal','your.earnings');
    $user = $this->get('security.context')->getToken()->getUser();
    $accountSummary = self::getAccountSummary($user);

    return $this->render('GDSiteBundle:User:userEarningsDetail.html.twig', $accountSummary);
  }

  /**
   * Support request made by the frontend user is managed through this action.
   *  
   * @Route("/new-request", name="gd_site_user_new_request")
   * @Template()
   *
   * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
   */
  public function userNewRequestAction(httpRequest $request) {
    
    $this->createBreadcrumbs('new.request', $request->get('_route'));
    $form = $this->createForm(new NewRequestType());

    if ($request->getMethod() == 'POST') {
      $form->bindRequest($request);
      $postedData = $request->request->get('gd_new_request');

      if ($form->isValid()) {
        
        $userRequest = new Request();
        $userRequest->setUser($this->get('security.context')->getToken()->getUser());
        $userRequest->setProblemId($postedData['problem']);
        $userRequest->setRequestDate(new \DateTime());
        $userRequest->setSubject($postedData['subject']);
        $userRequest->setMessage($postedData['message']);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($userRequest);
        $em->flush();
        
        $this->sendNewRequestEmailMessage($userRequest);
        $this->get('session')->setFlash('success', $this->get('translator')->trans('newrequest.sent.message',array('%requestNumber%' => $userRequest->getId()), 'flashmessages'));
        return $this->redirect($this->generateUrl('gd_site_user_new_request'));
      }
    }

    return $this->render('GDSiteBundle:User:newRequest.html.twig', array('form' => $form->createView()));
  }
  
  /**
   * The function to send a support request email
   * 
   * @param Request $userRequest
   * @return boolean 
   */
  
  public function sendNewRequestEmailMessage(Request $userRequest){
    
   try{      
      $renderTemplate = 'GDSiteBundle:Mail:sendRequestEmail.html.twig';
      $user = $this->get('security.context')->getToken()->getUser();
      $context = array('userRequest' => $userRequest,'user'=> $user);


      $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
      $toEmail = $this->container->getParameter('support_email');
      $this->get('mailer_utility')->sendEmailMessage($renderTemplate, $context,$fromEmail,$user->getEmail(),array(),$toEmail);
       return true;
    }
    catch(\Exception $e){
      return false;
    }  
    
  }

  /**
   * The user referral is managed through this action.
   * 
   * @Route("/refer-friend", name="gd_site_user_refer_friends")
   * @Template()
   *
   * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
   */
  public function userReferFriendAction(httpRequest $request) {
    
    $this->createBreadcrumbs('refer.friend',$request->get('_route'));
    $addresses = array();
    $form = $this->get('form.factory')->create(new ReferFriendType($this->get('translator')));

    if ($request->getMethod() == 'POST') {
      $referFriend = $request->request->get('referFriend');
      $form->bindRequest($request);
      
      if ($form->isValid()) {

        if (!empty($referFriend['email1'])) {
          $addresses[] = $referFriend['email1'];
        }

        if (!empty($referFriend['email2'])) {
          $addresses[] = $referFriend['email2'];
        }

        if (!empty($referFriend['email3'])) {
          $addresses[] = $referFriend['email3'];
        }

        if (!empty($referFriend['email4'])) {
          $addresses[] = $referFriend['email4'];
        }

        if (!empty($referFriend['email5'])) {
          $addresses[] = $referFriend['email5'];
        }
        
        $addresses = array_unique($addresses);
        if($this->sendReferralEmailMessage($referFriend,$addresses)){
          $this->get('session')->setFlash('email-notice', $this->get('translator')->trans('referralemail.sent.sucess',array(),'flashmessages'));
          $this->updateReferral($addresses);
        }else {
          $this->get('session')->setFlash('email-notice', $this->get('translator')->trans('referralemail.sent.error',array(),'flashmessages'));
        }

        return $this->redirect($this->generateUrl('gd_site_user_refer_friends'));
      }
    }
    return $this->render('GDSiteBundle:User:referFriend.html.twig', array('form' => $form->createView()));
  }
  
  /**
   * The function to send the referral email
   * 
   * @param Request $userRequest
   * @return boolean 
   */
  
  public function sendReferralEmailMessage($referFriend,$addresses = array()){
   
    $user = $this->get('security.context')->getToken()->getUser();
    $siteUrl = $this->generateUrl('gd_site_user_registration',array(),true);
    $registrationUrl = $this->generateUrl('gd_site_user_registration',array(),true);
    $sponsorshipCode = $user->getUsername();
    
   try {      
     
      $renderTemplate = 'GDSiteBundle:Mail:sendReferralEmail.html.twig';
      $context = array('referFriend' => $referFriend,'sponsorshipCode' => $sponsorshipCode,'siteUrl' => $siteUrl,'registrationUrl' => $registrationUrl);
      $fromEmail = $user->getEmail();
      $this->get('mailer_utility')->sendEmailMessage($renderTemplate, $context, $fromEmail, $addresses);
      
       return true;
    }
    catch(\Exception $e){
      return false;
    }  
    
  }

  /**
   * This function inserts a record to referral table
   * 
   * @param type $addresses 
   */
  private function updateReferral($addresses) {
    
    $user = $this->get('security.context')->getToken()->getUser();
    foreach ($addresses as $email) {
      $referral = new Referral();
      $referral->setReferralEmail($email);
      $referral->setCreatedAt(new \DateTime());
      $referral->setUser($user);

      try {
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($referral);
        $em->flush();
      } catch (\Exception $e) {
        $this->logger->err('****ERROR**** Referral with email: ' . $email . ' could not be saved for user with ID:' . $user->getId());
      }
    }
  }

  /**
   * @Route("/referral-detail", name="gd_site_user_referral_details")
   * @Template()
   *
   * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
   */
  public function userReferralDetailsAction(httpRequest $request) {
    
    $this->createBreadcrumbs('referral.details', $request->get('_route'));
    $user = $this->get('security.context')->getToken()->getUser();
    $referrals = $user->getReferrals();
    $referralUsers = $this->getDoctrine()->getEntityManager()->getRepository('GDAdminBundle:Referral')
                        ->getSubscribedReferralUsers($user);
    return $this->render('GDSiteBundle:User:referralDetails.html.twig', array('referrals' => $referrals, 'referralUsers' => $referralUsers));
  }

  /**
   *  To resend referral email from the my referrals page
   * 
   * @Route("/referral-email/{email}", name="gd_site_referral_email")
   * @Template()
   * @return array
   */
  public function sendReferralEmailAction($email) {
    
    $referralEmail = urldecode($email);
   
    if($this->resendReferralEmailMessage($referralEmail)){
          $notice = $this->get('translator')->trans('resendreferral.sent.sucess',array(),'flashmessages');
          $this->get('session')->setFlash('email-notice', $notice);
          $this->getDoctrine()->getEntityManager()->getRepository('GDAdminBundle:Referral')->updateReferralDate($referralEmail);
    }else {
          $notice = $this->get('translator')->trans('resendreferral.sent.error',array(),'flashmessages');
          $this->get('session')->setFlash('email-notice', $notice);
    }
    
    if ($this->getRequest()->isXmlHttpRequest()) {
      return new \Symfony\Component\HttpFoundation\Response($notice);
    }

    return array('notice' => $notice);
  }
  
  /**
   *
   * @param string $referralEmail
   * 
   * @return boolean 
   */
  public function resendReferralEmailMessage($referralEmail){
   
    $user = $this->get('security.context')->getToken()->getUser();
    $sponsorshipCode = $user->getUsername();
    
   try {      
      $renderTemplate = 'GDSiteBundle:Mail:resendReferralEmail.html.twig';
      $context = array('sponsorshipCode' => $sponsorshipCode,'siteUrl' => $this->generateUrl('gd_home',array(),true),'registrationUrl' => $this->generateUrl('gd_site_user_registration',array(),true));
      $fromEmail = $user->getEmail();
      $this->get('mailer_utility')->sendEmailMessage($renderTemplate,$context, $fromEmail, $referralEmail);
      
       return true;
    }
    catch(\Exception $e){
      return false;
    }  
    
  }

  /**
   *  Adds user's preferred merchant
   * 
   * @Route("/add/prefered-merchant/{slug}", name="gd_site_prefered_merchant")
   * @return array
   */
  public function addPreferedMerchantAction(Merchant $merchant) {
    try {
      $user = $this->get('security.context')->getToken()->getUser();
      $user->addPreferredMerchant($merchant);
      $em = $this->getDoctrine()->getEntityManager();
      $em->persist($user);
      $em->flush();
      $msg = $this->get('translator')->trans('merchants.preferredadd.success');
    } catch (\Exception $e) {
      $msg = $this->get('translator')->trans('merchants.preferredadd.error');
    }

    if ($this->getRequest()->isXmlHttpRequest()) {
      return new \Symfony\Component\HttpFoundation\Response($msg);
    }

    $this->get('session')->setFlash('success', $merchant->getName() .$msg);
    return $this->redirect($this->generateUrl('gd_merchant_show', array('slug' => $merchant->getSlug())));
  }

  /**
   * Removes user's preferred merchant
   * @Route("/remove/prefered-merchant/{slug}", name="gd_site_prefered_merchant_remove")
   * @return array
   */
  public function removePreferedMerchantAction(Merchant $merchant) {
    try {
      $user = $this->get('security.context')->getToken()->getUser();
      $user->removePreferedMerchant($merchant);
      $em = $this->getDoctrine()->getEntityManager();
      $em->flush();
      $msg = $this->get('translator')->trans('merchants.preferredremove.success');
    } catch (\Exception $e) {
      $msg = $this->get('translator')->trans('merchants.preferredremove.error');
    }

    if ($this->getRequest()->isXmlHttpRequest()) {
      return new \Symfony\Component\HttpFoundation\Response($msg);
    }

    $this->get('session')->setFlash('success', $merchant->getName() . $msg);
    return $this->redirect($this->generateUrl('gd_merchant_show', array('slug' => $merchant->getSlug())));
  }
  
  /**
   * @Route("/referral/content", name="gd_site_referral_content") 
   */
  public function userReferralContentAction(){
     $siteUrl = $this->generateUrl('gd_site_user_registration',array(),true);
     $registrationUrl = $this->generateUrl('gd_site_user_registration',array(),true);
     $user = $this->get('security.context')->getToken()->getUser();
     $sponsorshipCode = $user->getUsername();
                 
    return $this->render('GDSiteBundle:User:userReferralContent.html.twig',array('sponsorshipCode' => $sponsorshipCode,'siteUrl' => $siteUrl,'registrationUrl' => $registrationUrl));
  }
  
  /**
   * @Route("/legal/confirm", name="gd_site_legal_confirm") 
   */
  public function confirmLegalAction(httpRequest $request){
    
    $user = $this->container->get('security.context')->getToken()->getUser();
    
    if($user->getIsOlduser()){  
    
     $form = $this->createForm(new ConfirmLegalType());
     
     if ($request->getMethod() == 'POST') {
       $form->bindRequest($request);                 
          if ($form->isValid()) {
            $user->setIsOlduser(false);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            
            $this->get('session')->setFlash('success', $this->get('translator')->trans('olduser.legalconfirm.message',array(), 'flashmessages'));
            return $this->redirect($this->generateUrl('fos_user_profile_show'));
            
        }
        
     }
     return $this->render('GDSiteBundle:User:legalContent.html.twig', array('form' => $form->createView()));
    }
       return $this->redirect($this->generateUrl('gd_top_merchants'));
  }
}