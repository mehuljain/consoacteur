<?php

namespace GD\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CategoryAdminController extends Controller
{
    /**
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
                $this->admin->delete($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_delete_success');
            } catch ( ModelManagerException $e ) {
                // Custom code to display error messages on foreign key constraints
                $tags = $object->getTags();
                $modules = $object->getModules();
                $merchants = $object->getMerchants();
                $message = $object.' could not be deleted because it is linked with';
                for($i = 0 ; $i < sizeof($tags); $i++) {
                    $message .= (0 === $i) ? ' Tags:' : '';
                    $message .= ' '.$tags[$i].',';
                }
                $message = rtrim($message, ',');
                for($i = 0 ; $i < sizeof($modules); $i++) {
                    $message .= (0 === $i && sizeof($tags) > 0) ? ' |' : '';
                    $message .= (0 === $i)? ' Modules:' : '';
                    $message .= ' '.$modules[$i].',';
                }
                $message = rtrim($message, ',');
                for($i = 0 ; $i < sizeof($merchants) ; $i++) {
                    $message .= (0 === $i && (sizeof($tags) + sizeof($modules)) > 0) ? ' |' : '';
                    $message .= (0 === $i) ? ' Merchants:' : '';
                    if (1 === sizeof($merchants) + sizeof($tags)) {
                        $message .= ' '.$merchants[$i];
                    } elseif (1 !== (sizeof($merchants) + sizeof($tags)) && sizeof($merchants) === $i+1 ) {
                        $message = rtrim($message, ',');
                        $message .= ' and '.$merchants[$i];
                    } else {
                        $message .= ' '.$merchants[$i].',';
                    }
                }
                $message = rtrim($message, ',');
                $this->get('session')->setFlash('sonata_flash_error', $message);
            }

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('SonataAdminBundle:CRUD:delete.html.twig', array(
            'object' => $object,
            'action' => 'delete'
        ));
    }
}
