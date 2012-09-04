<?php

namespace GD\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CustomerAdminController extends Controller
{
    /*
     *  Reset password on customer request.
     *  A change password email is sent to the customer's registered ID
     */
    public function resetPasswordAction($id = null)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        $randString = rand(10000000,2000000000);

        $userManager = $this->admin->getUserManager();
        try {
            $object->setPlainPassword($randString);
            $this->admin->update($object);
            $this->sendResetPasswordEmail($randString,$object);
            $this->get('session')->setFlash('sonata_flash_success', 'flash_password_reset_success');
        } catch ( \Exception $e ) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_password_reset_error');
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    /**
     * @param $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * Blacklist a customer
     */
    public function blacklistAction($id)
    {
        if (false === $this->admin->isGranted('BLACKLIST')) {
            throw new AccessDeniedException();
        }

        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }     

        try {
            $object->setEnabled(false);
            $object->setBlacklisted(true);
            $object->setBlacklistedAt(new \DateTime('now'));
            $this->admin->update($object);
            $this->get('session')->setFlash('sonata_flash_success', 'flash_blacklist_success');
        } catch (ModelManagerException $e) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_blacklist_error');
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * Archive a customer
     */
    public function archiveAction($id)
    {
        if (false === $this->admin->isGranted('ARCHIVE')) {
            throw new AccessDeniedException();
        }

        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if ($this->getRequest()->getMethod() == 'ARCHIVE') {
            try {
                $object->setEnabled(false);
                $object->setIsArchived(true);
                $object->setArchivedAt(new \DateTime('now'));
                $this->admin->update($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_archive_success');
            } catch (ModelManagerException $e) {
                $this->get('session')->setFlash('sonata_flash_error', 'flash_archive_error');
            }

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('GDAdminBundle:CRUD:archive.html.twig', array(
            'object' => $object,
            'action' => 'archive'
        ));
    }
    
    /*
     *  Save a blacklist reason
     */
    public function setBlacklistReasonAction($customerId, $reason)
    {
        $customer = $this->getDoctrine()->getRepository('GDAdminBundle:User')->find($customerId);
        try {
            $customer->setBlacklistReason($reason);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($customer);
            $em->flush();
            $this->get('session')->setFlash('sonata_flash_success', 'The customer has been blacklisted');
        } catch (ModelManagerException $e) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_blacklist_error');
            return new Response('false', 200);
        }
        return new Response('true', 200);
    }
    
    /*
     *  Send a reset password email to the customer
     */
    public function sendResetPasswordEmail($password, $object)
    {
      $renderTemplate = 'GDSiteBundle:Mail:sendResetPassword.html.twig';
      $siteUrl = $this->get('router')->generate('gd_home', array(), true);
      $context = array('password' => $password,'siteUrl' => $siteUrl, 'username' => $object->getUsername());
      $fromEmail = $this->container->getParameter('fos_user.registration.confirmation.from_email');      
      $this->get('mailer_utility')->sendEmailMessage($renderTemplate, $context,$fromEmail,$object->getEmail());
       return true;
    }
}
