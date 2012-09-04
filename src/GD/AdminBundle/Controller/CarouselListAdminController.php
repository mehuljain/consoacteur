<?php

namespace GD\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class CarouselListAdminController extends Controller
{
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

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        // Decide the merchant list
        if ('cashback-merchants' == $object->getName()) {
            $this->admin->setOfferType(\GD\AdminBundle\Entity\Offer::OFFER_TYPE_CASHBACK);
        } elseif ('codepromo-merchants' == $object->getName()) {
            $this->admin->setOfferType(\GD\AdminBundle\Entity\Offer::OFFER_TYPE_CODE_PROMO);
        }

        $this->admin->setSubject($object);

        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {

                $data = $this->get('request')->request->get($this->admin->getUniqId());
                $object->updateSortOrder($data['merchants']);

                $this->admin->update($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_edit_success');

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

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
}
