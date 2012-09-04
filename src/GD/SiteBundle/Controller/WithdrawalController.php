<?php

namespace GD\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GD\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use GD\SiteBundle\Form\Type\Withdrawal\WithdrawOptionsType;
use GD\AdminBundle\Entity\Withdrawal;
use GD\AdminBundle\Entity\Transaction;
use GD\SiteBundle\Form\Type\Withdrawal\BankWithdrawalType;
use GD\SiteBundle\Form\Type\Withdrawal\ChequeWithdrawalType;
use GD\SiteBundle\Form\Type\Withdrawal\PaypalWithdrawalType;
use Symfony\Component\Form\FormError;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
/**
 * @Route("/secured")
 */
class WithdrawalController extends Controller
{
    /**
     * Creates the breadcrumb link for the Withdrawal page 
     */
    private function createIndexBreadcrumbs()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($this->get('translator')->trans('home.page', array(), 'breadcrumb'), $this->get("router")->generate("gd_home"));
        $breadcrumbs->addItem($this->get('translator')->trans('your.earnings', array(), 'breadcrumb'), $this->get("router")->generate("gd_site_withdrawal"));
    }

    /**
     * Renders the Withdrawal options in the User Earnings page.
     * 
     * @Route("/withdrawal", name="gd_site_withdrawal")
     * @Template()
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $this->createIndexBreadcrumbs();
        $withdrawallimits = array('bank' => $this->container->getParameter('bank_withdrawal_limit'),
                                  'cheque' => $this->container->getParameter('cheque_withdrawal_limit'),
                                  'paypal' => $this->container->getParameter('paypal_withdrawal_limit')
                                 );
        $form = $this->createForm(new WithdrawOptionsType($this->container->getParameter('withdrawal_bank_type'),$this->container->getParameter('currency'),$this->get('translator'),$withdrawallimits));

        $user = $this->get('security.context')->getToken()->getUser();
        $accountSummary = \GD\SiteBundle\Controller\UserController::getAccountSummary($user);
        $checkArray = array();
        $transactions = $user->getTransactions();
        foreach($transactions as $test){
          if( is_null($test->getWithdrawal()) && Transaction::TRANSACTION_STATUS_CONFIRMED === $test->getStatus()) {
              $checkArray[$test->getId()] = $test->getVersion();
          }
        }
        
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $checkArray = serialize($checkArray);
                return $this->redirect($this->generateUrl('gd_site_withdrawal_transaction', array('type' => $data['type'], 'amount' => $accountSummary['withdrawableAmount'], 'checkArray' => $checkArray)));
            }
        }

        $formView = $form->createView();
        return array('formView' => $formView, 'accountSummary' => $accountSummary);
    }

    /**
     * @Route("/withdrawal/type/{type}/amount/{amount}", name="gd_site_withdrawal_transaction")
     * @Template()
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $type
     * @param $amount
     * @return array
     */
    public function withdrawalAction(Request $request, $type, $amount, $checkArray = NULL)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($this->get('translator')->trans('home.page', array(), 'breadcrumb'), $this->get("router")->generate("gd_home"));

        $user = $this->get('security.context')->getToken()->getUser();

        
        $withdrawableAmount = $this->getWithdrawableAmount($user); 
        if (bccomp($amount , $withdrawableAmount , 2) != 0) { // Amount different from WithdrawableAmount by changing the URL
            $this->getRequest()->getSession()->setFlash('error', $this->get('translator')->trans('withdrawal.invalidamount.message',array('%withdrawableAmount%' => $withdrawableAmount), 'flashmessages'));
            return $this->redirect($this->generateUrl('gd_site_withdrawal'));
        }

        $withdrawal = new Withdrawal($type,$amount,$user);
        
        if (Withdrawal::WITHDRAWAL_TYPE_BANK_1 == $type || Withdrawal::WITHDRAWAL_TYPE_BANK_2 == $type) {
            $breadcrumbs->addItem($this->get('translator')->trans('withdrawal.bank', array(), 'breadcrumb'), $this->get("router")->generate("gd_site_withdrawal_transaction", array('type' => $type, 'amount' => $amount)));
            $form = $this->createForm(new BankWithdrawalType($type), $withdrawal);
            $minimumAmount = $this->container->getParameter('bank_withdrawal_limit');
        } elseif (Withdrawal::WITHDRAWAL_TYPE_CHEQUE == $type) {
            $breadcrumbs->addItem($this->get('translator')->trans('withdrawal.cheque', array(), 'breadcrumb'), $this->get("router")->generate("gd_site_withdrawal_transaction", array('type' => $type, 'amount' => $amount)));
            $form = $this->createForm(new ChequeWithdrawalType(), $withdrawal);
            $minimumAmount = $this->container->getParameter('cheque_withdrawal_limit');
        } elseif (Withdrawal::WITHDRAWAL_TYPE_PAYPAL == $type) {
            $breadcrumbs->addItem($this->get('translator')->trans('withdrawal.paypal', array(), 'breadcrumb'), $this->get("router")->generate("gd_site_withdrawal_transaction", array('type' => $type, 'amount' => $amount)));
            $translator = $this->get('translator');
            $form = $this->createForm(new PaypalWithdrawalType($translator), $withdrawal);
            $minimumAmount = $this->container->getParameter('paypal_withdrawal_limit');
        }

        if (bccomp($withdrawableAmount,$minimumAmount, 2) === -1) { 
            $translateType = $this->get('translator')->trans($withdrawal->getTypeAsString());            
            $this->getRequest()->getSession()->setFlash('error', $this->get('translator')->trans('withdrawal.lowamount.message',array('%minimumAmount%' => $minimumAmount,'%type%' => $translateType,'%currency%'=> $this->container->getParameter('currency')), 'flashmessages'));
            return $this->redirect($this->generateUrl('gd_site_withdrawal'));
        }
        //TODO: Check if the specific type of withdrawal is enabled in the backend. Maybe create a Preference entity.

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            $currentDateTime = new \DateTime('now');
            if ($user->getWithdrawalCode() != $withdrawal->getCode()) { 
               $form->addError(new FormError($this->get('translator')->trans('withdrawal.code.invalid',array(),'validators')));
            }
            else if($currentDateTime > $user->getWithdrawalCodeExpiresAt() && $user->getWithdrawalCode() == $withdrawal->getCode()){
                $form->addError(new FormError($this->get('translator')->trans('withdrawal.code.expired',array(),'validators')));
            }
            
            //Added another check for the minimum amount when user submits from the Withdrawal request page
            if (bccomp($this->getWithdrawableAmount($user) ,$minimumAmount, 2) === -1) { 
              $this->getRequest()->getSession()->setFlash('error', $this->get('translator')->trans('withdrawal.lowamount.message',array('%minimumAmount%' => $minimumAmount,'%type%' => $withdrawal->getTypeAsString()), 'flashmessages'));
              return $this->redirect($this->generateUrl('gd_site_withdrawal'));
            }

            $em = $this->getDoctrine()->getEntityManager();

            if ($form->isValid()) {
                try {                  
                    $transactions = $user->getTransactions();
                    $checkArray = unserialize($checkArray);
                    foreach ($transactions as $transaction) {                        
                        if (is_null($transaction->getWithdrawal())) { // Ignore Transactions that have already been marked with a previous Withdrawal Request
                            if (Transaction::TRANSACTION_STATUS_CONFIRMED === $transaction->getStatus()) {
                                try{
                                //Optimistic locking to double check the transactions
                                $em->find('GDAdminBundle:Transaction', $transaction->getId(), LockMode::OPTIMISTIC, $checkArray[$transaction->getId()]);
                                $transaction->setWithdrawal($withdrawal);
                                $transaction->setStatus(Transaction::TRANSACTION_STATUS_PAYMENT_REQUESTED);
                                $em->persist($transaction);
                                }
                                catch (OptimisticLockException $e){
                                  $this->getRequest()->getSession()->setFlash('error', $this->get('translator')->trans('withdrawal.invalidamount.message',array('%withdrawableAmount%' => $withdrawableAmount,'%currency%' => $this->container->getParameter('currency')), 'flashmessages'));
                                  return $this->redirect($this->generateUrl('gd_site_withdrawal'));
                                }
                            }
                        }
                    }

                    $withdrawal->setRequestedAt(new \DateTime('now'));
                    $withdrawal->setUsername($user->getUsername());
                    $user->setWithdrawalCodeExpiresAt(new \DateTime('now'));
                    $em->persist($user);
                    $em->persist($withdrawal);
                    $em->flush();                    
                    
                    
                    $this->sendWithdrawalRequestEmailMessage($user,$withdrawal);
                    $this->getRequest()->getSession()->setFlash('success', $this->get('translator')->trans('withdrawal.requestsuccess.message',array('%withdrawalId%' => $withdrawal->getId()), 'flashmessages'));

                } catch (\Exception $e){
                    
                    $this->getRequest()->getSession()->setFlash('error', $this->get('translator')->trans('withdrawal.requesterror.message',array(), 'flashmessages'));
                    $logger = $this->get('logger');
                    $logger->err('****ERROR**** Transaction could not be created for a Withdrawal for user with ID:'.$user->getId());
                }

                return $this->redirect($this->generateUrl('gd_site_withdrawal'));
            }
        }

        $formView = $form->createView();
        $formView->getChild('amount')->setAttribute('value', $amount);

        return array('form' => $formView);
    }

    /**
     * @Route("/withdrawal/code", name="gd_site_withdrawal_get_code")
     * @Template()
     * @return array
     */
    public function codeAction()
    {
        $code = rand(10000000,1000000000);

        $user = $this->get('security.context')->getToken()->getUser();
        $user->setWithdrawalCode($code);
        $user->setWithdrawalCodeExpiresAt(new \DateTime('+30 minutes'));

        $this->getDoctrine()->getEntityManager()->persist($user);
        $this->getDoctrine()->getEntityManager()->flush();
        
        if($this->sendWithdrawalCodeEmailMessage($user))
        {
          $notice = $this->get('translator')->trans('withdrawal.coderequest.success');
        }
        else {
          $notice = $this->get('translator')->trans('withdrawal.coderequest.error');
        }        

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response($notice);
        }

        return array('notice' => $notice);
    }
   
    /**
     *
     * @param User $userRequest
     * @return boolean 
     */
   public function sendWithdrawalCodeEmailMessage(User $user){
    
    try{      
        $renderTemplate = 'GDSiteBundle:Mail:withdrawalCodeEmail.html.twig';
        $context = array('user' => $user);

        $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');

        $this->get('mailer_utility')->sendEmailMessage($renderTemplate, $context,$fromEmail, $user->getEmail());
        return true;
      }
      catch(\Exception $e){
        return false;
      }  
    
   }
    /**
     * This function sends the Withdrawal request confirmation mail to the user.
     * @param User $userRequest
     * @return boolean 
     */
   public function sendWithdrawalRequestEmailMessage(User $user, Withdrawal $withdrawal){
    
    try{      
        $renderTemplate = 'GDSiteBundle:Mail:withdrawalRequestEmail.html.twig';
        $context = array('user' => $user,'withdrawal' => $withdrawal);

        $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');

        $this->get('mailer_utility')->sendEmailMessage($renderTemplate,$context,$fromEmail, $user->getEmail());
        return true;
      }
      catch(\Exception $e){
        return false;
      }  
    
   }
    
    /**
     * @Route("/receive-account-details-email", name="gd_site_withdrawal_receive_account_details_email")
     * @Template()
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function sendAccountDetailsEmailAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $accountSummary = \GD\SiteBundle\Controller\UserController::getAccountSummary($user);

        $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');

        $context = array('accountSummary' => $accountSummary,'currency'=> $this->container->getParameter('currency'));
        $renderTemplate = 'GDSiteBundle:Mail:accountDetailsEmail.txt.twig';

        $this->get('mailer_utility')->sendEmailMessage($renderTemplate,$context, $fromEmail, $user->getEmail());
        $this->get('session')->setFlash('success', $this->get('translator')->trans('withdrawal.amountdetails.message',array(), 'flashmessages'));

        return $this->redirect($this->generateUrl('gd_site_withdrawal'));
    }
    
    /**
     *  A utility function to get the most updated withdrawable amount from the database.
     * 
     * @param User $user
     * @return float  
     */
    public static function getWithdrawableAmount($user){
      
      $accountSummary = \GD\SiteBundle\Controller\UserController::getAccountSummary($user);
      return $accountSummary['withdrawableAmount'];
    }
}
