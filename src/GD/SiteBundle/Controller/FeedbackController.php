<?php

namespace GD\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GD\AdminBundle\Entity\Feedback;
use GD\AdminBundle\Entity\Merchant;
use GD\AdminBundle\Form\FeedbackType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FeedbackController extends Controller
{
    /**
     * @Route("/feedback/create", name="gd_site_user_feedback_create")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse
     */
    public function createFeedbackAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        
        $merchant = $this->getDoctrine()->getRepository('GDAdminBundle:Merchant')->findOneBy(array('id' => $request->request->get('id')));

        $ip = $this->container->get('request')->getClientIp();
        if ($request->getMethod() == 'POST') {
            $rating = $request->request->get('rating');
            $comment = $request->request->get('feedback');
            $user = $this->get('security.context')->getToken()->getUser();

            $feedback = new Feedback();
            $feedback->setIpaddress($ip);
            $feedback->setRating($rating);
            $feedback->setComment($comment);
            $feedback->setCreatedAt(new \DateTime('now'));
            $feedback->setUpdatedAt(new \DateTime('now'));
            $feedback->setUser($user);
            $feedback->setMerchant($merchant);

            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($feedback);
                $em->flush();
                $this->get('session')->setFlash('feedback-success', $this->get('translator')->trans('feedback.success.message',array(), 'flashmessages'));
            } catch (\Exception $e) {
                $this->get('session')->setFlash('feedback-error', $this->get('translator')->trans('feedback.error.message',array(), 'flashmessages'));
                $logger = $this->get('logger');
                $logger->err('****ERROR**** Feedback could not be created for User with ID: '.$user->getId().' for Merchant with ID:'.$merchant->getId());
            }

            return $this->redirect($this->generateUrl('gd_merchant_show', array('slug' => $merchant->getSlug())));
        }

        return $this->redirect($this->generateUrl('gd_merchant_show', array('slug' => $merchant->getSlug())));
    }
}
