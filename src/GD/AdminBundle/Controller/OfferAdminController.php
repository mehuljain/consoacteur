<?php

namespace GD\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OfferAdminController extends Controller
{
    /**
     * Archive an Offer
     *
     * @param $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
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
                $object->setIsArchived(true);
                $object->setIsCurrent(false);
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
    
    /**
     * return the Response object associated to the edit action
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id = null)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());
        $this->admin->setOfferId($id);
        
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

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

    /**
     * Duplicate an offer. A uniqueId is appended to the offer name. 
     *
     * @param $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function duplicateAction($id)
    {
        if (false === $this->admin->isGranted('DUPLICATE')) {
            throw new AccessDeniedException();
        }

        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if ($this->getRequest()->getMethod() == 'DUPLICATE') {
            try {
                $uniqueId = uniqid();                
                $offer = clone($object);
                $offer->resetId();
                $offer->resetPrimaryCategory();
                $offer->resetPrimaryTag();
                $offer->setName($object->getName().'-'.$uniqueId);
                $offer->setIsArchived(false);
                $offer->setArchivedAt(null);
                // TODO - Find a way of cloning associated ArrayCollections

                $this->admin->create($offer);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_duplicate_success');
            } catch (ModelManagerException $e) {
                $this->get('session')->setFlash('sonata_flash_error', 'flash_duplicate_error');
            }

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render($this->admin->getEditTemplate(), array(
            'action' => 'edit',
            'object' => $object,
        ));
    }

}
