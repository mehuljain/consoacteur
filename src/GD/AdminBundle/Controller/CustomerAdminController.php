<?php

namespace GD\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

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
    
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportAction(Request $request)
    {
        $format = $request->get('format');

        $allowedExportFormats = (array) $this->admin->getExportFormats();

        if(!in_array($format, $allowedExportFormats) ) {
            throw new \RuntimeException(sprintf('Export in format `%s` is not allowed for class: `%s`. Allowed formats are: `%s`', $format, $this->admin->getClass(), implode(', ', $allowedExportFormats)));
        }

        $filename = sprintf('export_%s_%s.%s',
            strtolower(substr($this->admin->getClass(), strripos($this->admin->getClass(), '\\') + 1)),
            date('Y_m_d_H_i_s', strtotime('now')),
            $format
        );
        
        $loop = 0; 
        $end = 0;
        $offset = 50000;//Collect these many records and then loop again for further records
        $datagrid = $this->admin->getDatagrid();        
        $datagrid->buildPager();
        $count = (int)$datagrid->getPager()->count();
        $count = ceil($count/$offset);
        
        set_time_limit(0);
        ini_set('memory_limit','512M');
        $data = array();        
        $content = $this->renderView('GDAdminBundle:Default:adminCsvHeader.html.twig');
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);        
        $response->setContent($content);
        $response->send();
        $em = $this->getDoctrine()->getEntityManager();
        while($loop < $count){
          $start = 1 + $end;
          $end = $end + $offset;
          $query = $em->createQuery('SELECT u.email,u.username,u.newsletterSubscription,u.salutation,u.enabled FROM GD\AdminBundle\Entity\User u where u.isArchived = false');
          $query->setFirstResult($start);
          $query->setMaxResults($offset);
          $data = $query->getArrayResult();
          $content = $this->renderView('GDAdminBundle:Default:adminCsv.html.twig', array('data' => $data));
          $response->setContent($content);
          $response->send();
          unset($query);
          unset($data);
          unset($content);
          $loop++;
        }
        
        $content = 'End of file';

        return new Response($content);
    }    

}