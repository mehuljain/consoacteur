<?php

namespace GD\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GD\AdminBundle\Entity\User;

  /**
   * @Route("/auth/login", name="fos_user_security_login")
   * 
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   * @throws NotFoundHttpException /
   */
class SecurityController extends ContainerAware
{
    /**
     * This function is called through the login path defined in security.yml configuration file. Please
     * note that Symfony does the authentication of the user and only provides the authentication response( sucess or error)
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse 
     */
    public function loginAction()
    {
        // redirect logged in users to the homepage
        if($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
           $user = $this->container->get('security.context')->getToken()->getUser();
           if($user->getIsOlduser())
            {
              return new RedirectResponse($this->container->get('router')->generate('gd_site_legal_confirm'));
            }
            return new RedirectResponse($this->container->get('router')->generate('gd_top_merchants'));
        }

       /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->container->get('request');
        /* @var $session \Symfony\Component\HttpFoundation\Session */
        $session = $request->getSession();
        $ajaxLogin = $request->get('ajaxLogin');     
        $checkActivation = 1;        
           
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) { 
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }
        
        if($error instanceof \Symfony\Component\Security\Core\Exception\BadCredentialsException){
            $checkActivation = 0;
        }
        
        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();    
        }
                
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($lastUsername);
        /**
         * Added customization for users who have not yet registered their account
         */ 
        if(!empty($user) && $checkActivation == 1){
            if(self::checkActivation($user)){              
                if(!$user->isEnabled()){
                   $this->container->get('session')->setFlash('user_activate', $this->container->get('translator')->trans('login.notactivated.message',array(), 'flashmessages'));
                  return new RedirectResponse($this->container->get('router')->generate('gd_site_user_resetting'));
                }
            }
        }
        
        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        
        if ($request->isXmlHttpRequest() || $ajaxLogin ) {
                    
           return $this->container->get('templating')->renderResponse('GDSiteBundle:Security:login_ajax.html.twig',
                   array('last_username' => $lastUsername,
                         'error'         => $error,
                         'csrf_token'    => $csrfToken));
        }
                 
        return $this->container->get('templating')->renderResponse('GDSiteBundle:Security:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token' => $csrfToken
        ));
    }
    
    /**
     * Returns true for a valid user and false for invalid users.
     * @param User $user 
     * @return boolean
     */
    static public function checkActivation(User $user){

      if(!$user->getIsAdminUser() && 
         !$user->isBlacklisted() && 
         !$user->getIsArchived() &&
         $user->isAccountNonExpired() && 
         $user->isAccountNonLocked())
      {
        return TRUE;
      }
      return FALSE;
    }
}
