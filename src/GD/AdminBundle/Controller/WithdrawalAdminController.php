<?php

namespace GD\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GD\AdminBundle\Entity\Withdrawal;
use GD\AdminBundle\Entity\Transaction;


class WithdrawalAdminController extends Controller
{
    /*
     *  Send an email to user when their Withdrawal has been put on hold
     */ 
    private function sendOnHoldEmailToUser($withdrawal)
    {
      $user = $withdrawal->getUser();
      $renderTemplate = 'GDSiteBundle:Mail:sendOnHoldEmail.html.twig';
      $context = array('message' => $withdrawal->getUserComment());
      $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');
      $toEmail = $this->container->getParameter('support_email');
      $this->get('mailer_utility')->sendEmailMessage($renderTemplate, $context,$fromEmail,$user->getEmail(),array(),$toEmail);
     
      $flashMessages = $this->get('session')->getFlashes();
      $flashMessages['sonata_flash_success'] .= ', '.'on_hold_email_sent_success';
      $this->get('session')->setFlashes($flashMessages);
      return true; 
    }

    /**
     * return the Response object associated to the edit action
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id = null, $selectedId = null)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        /* Setting the Withdrawal type in Admin */
        $this->admin->setWithdrawalType($object->getType());
        
        
        $this->admin->setSubject($object);

        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                $this->admin->update($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_edit_success');

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                // redirect to edit mode
                return $this->redirectTo($object);
            }

            $this->get('session')->setFlash('sonata_flash_error', 'flash_edit_error');
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getEditTemplate(), array(
            'action' => 'edit',
            'form'   => $view,
            'object' => $object,
        ));
    }
    
    /*
     * return the Response object associated to the edit action when status is TRANSACTION_STATUS_APPROVED
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function approveAction($id = null, $selectedId = null)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());
        
        $object = $this->admin->getObject($id);
        
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        try {
            $object->setStatus(Transaction::TRANSACTION_STATUS_APPROVED);
            $object->setValidatedAt(new \DateTime('now'));
            $transactions = $object->getTransactions();
            foreach($transactions as $transaction) {
                $transaction->setStatus(Transaction::TRANSACTION_STATUS_APPROVED);
            }
            $this->admin->update($object);
            $this->get('session')->setFlash('sonata_flash_success', 'flash_approve_success');
        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_approve_error');
            $logger = $this->get('logger');
            $logger->err('****ERROR**** Transaction could not be created for a Withdrawal Status Approve with ID:'.$object->getId());
        }
        
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /*
     * return the Response object associated to the edit action when status is TRANSACTION_STATUS_ON_HOLD
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function holdAction($id = null, $selectedId = null)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());
        
        $object = $this->admin->getObject($id);
        
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        try {
            $object->setStatus(Transaction::TRANSACTION_STATUS_ON_HOLD);
            $object->setProcessedAt(new \DateTime('now'));
            $transactions = $object->getTransactions();
            foreach($transactions as $transaction) {
                $transaction->setStatus(Transaction::TRANSACTION_STATUS_ON_HOLD);
            }
            $this->admin->update($object);
            $this->get('session')->setFlash('sonata_flash_success', 'flash_on_hold_success');
        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_on_hold_error');
            $logger = $this->get('logger');
            $logger->err('****ERROR**** Transaction could not be created for a Withdrawal Status On Hold with ID:'.$object->getId());
        }

        $this->sendOnHoldEmailToUser($object);

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /*
     * return the Response object associated to the edit action when status is TRANSACTION_STATUS_PAID
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function paidAction($id = null, $selectedId = null)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        try {
            $object->setStatus(Transaction::TRANSACTION_STATUS_PAID);
            $object->setPaidAt(new \DateTime('now'));
            $transactions = $object->getTransactions();
            foreach($transactions as $transaction) {
                $transaction->setStatus(Transaction::TRANSACTION_STATUS_PAID);
            }
            $this->admin->update($object);
            $this->get('session')->setFlash('sonata_flash_success', 'flash_paid_success');
        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_paid_error');
            $logger = $this->get('logger');
            $logger->err('****ERROR**** Transaction could not be created for a Withdrawal Status On Hold with ID:'.$object->getId());
        }

        return new RedirectResponse($this->admin->generateUrl('list'));

    }

    /**
     * Delete a Withdrawal. All transactions corresponding to the Withdrawal are marked with status TRANSACTION_STATUS_CONFIRMED
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @param $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('DELETE', $object)) {
            throw new AccessDeniedException();
        }

        if ($this->getRequest()->getMethod() == 'DELETE') {
            try {
                $transactions = $object->getTransactions();

                foreach ($transactions as $transaction) {
                    $object->getTransactions()->removeElement($transaction); //Removing association mapping 
                    $transaction->unsetWithdrawal();
                    $transaction->setStatus(Transaction::TRANSACTION_STATUS_CONFIRMED);
                }

                $this->admin->delete($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_delete_success');
            } catch ( ModelManagerException $e ) {
                $this->get('session')->setFlash('sonata_flash_error', 'flash_delete_error');
            }

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('SonataAdminBundle:CRUD:delete.html.twig', array(
            'object' => $object,
            'action' => 'delete'
        ));
    }
}
