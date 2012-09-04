<?php

namespace GD\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use GD\AdminBundle\Entity\Newsletter;
use GD\AdminBundle\Entity\User;
use GD\SiteBundle\Form\Type\RegistrationType;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="gd_home")
     * @Template()
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function indexAction()
    {
        // Show homepage only to anonymous users. 
        if (true === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->get("router")->generate("gd_merchant_list", array('type' => 'top', 'filter' => 'list')));
        }
        
        return array();   
    }

    /**
     * Creates breadcrumbs for the pages.
     * 
     * @param string $page 
     */
    private function createPageBreadcrumbs($page)
    {
        if(!empty($page)){
          $breadcrumbs = $this->get("white_october_breadcrumbs");
          $breadcrumbs->addItem($this->get('translator')->trans('home.page', array(), 'breadcrumb'), $this->get("router")->generate("gd_home"));
          $breadcrumbs->addItem($this->get('translator')->trans($page->getSlug(), array(), 'breadcrumb'), $this->get("router")->generate("gd_pages", array('slug' => $page->getSlug())));
        }
    }

    /**
     * @param $slug
     *
     * @Route("/pages/{slug}", name="gd_pages")
     * @Template()
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function pageAction($slug)
    {
        $page = $this->getDoctrine()->getRepository('GDAdminBundle:Page')->findOneBy(array('slug' => $slug));
        $this->createPageBreadcrumbs($page);

        if (!$page) {
          //redirect to Home page 
           return $this->redirect($this->generateUrl('gd_home'));
        }
        
        return array('translatedPage' => $page);
    }

    /**
     * @Route("/how-does-it-work", name="gd_how_it_works")
     * @Template()
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function howDoesItWorkAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($this->get('translator')->trans('home.page', array(), 'breadcrumb'), $this->get("router")->generate("gd_home"));
        $breadcrumbs->addItem($this->get('translator')->trans('how.it.works', array(), 'breadcrumb'), $this->get("router")->generate("gd_how_it_works"));
        
        return $this->render('GDSiteBundle:Default:gdworking.html.twig');
    }

    /**
     * @Route("/menu", name="gd_site_menu")
     * @Template()
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function menuAction()
    {
        $modules = $this->getDoctrine()->getRepository('GDAdminBundle:Module')->findBy(array(), array(), 4);

        return array('modules' => $modules);
    }
    
    /**
     * Provides the listing of new merchants sidebar.
     * 
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response 
     */
    public function newMerchantsSidebarAction()
    {
        $merchantList = $this->getDoctrine()
                            ->getRepository('GDAdminBundle:MerchantList')
                            ->findOneBy(array('name' => 'new-merchants'));
        
        $newMerchants = $merchantList->getMerchants();
        
        return $this->render('GDSiteBundle:Default:new_merchants.html.twig', array('newMerchants' => $newMerchants));  
    }
    
    /**
     * Provides the listing of top merchants sidebar.
     * 
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response 
     */
    public function topMerchantsSidebarAction()
    {
        $merchantList = $this->getDoctrine()
                            ->getRepository('GDAdminBundle:MerchantList')
                            ->findOneBy(array('name' => 'top-merchants'));

        $topMerchants = $merchantList->getMerchants();

        return $this->render('GDSiteBundle:Default:top_merchants.html.twig', array('topMerchants' => $topMerchants));
    }
    
    /**
     * Cashback and Code Promo Carousels in the Home Page
     * 
     * @Route("/carousel", name="gd_site_carousel")
     * @Template()
     * 
     * @return array Cashback and Code Promo Carousel Merchants
     */
    public function carouselAction()
    {
        $cashbackOffers = $this->getDoctrine()->getRepository('GDAdminBundle:Offer')->getOffersByMerchantsList('cashback-merchants', \GD\AdminBundle\Entity\Offer::OFFER_TYPE_CASHBACK);

        $cashbackCarousel = array();
        foreach ($cashbackOffers as $o) {
            $cashbackCarousel[$o->getMerchant()->getId()]['merchant_name'] = $o->getMerchant()->getName();
            $cashbackCarousel[$o->getMerchant()->getId()]['merchant_image'] = $o->getMerchant()->getImage();
            $cashbackCarousel[$o->getMerchant()->getId()]['display_value'] = $o->getUserGainValue();
            $cashbackCarousel[$o->getMerchant()->getId()]['merchant_slug'] = $o->getMerchant()->getSlug();
            $cashbackCarousel[$o->getMerchant()->getId()]['merchant_id'] = $o->getMerchant()->getId();
        }

        $codepromoOffers = $this->getDoctrine()->getRepository('GDAdminBundle:Offer')->getOffersByMerchantsList('codepromo-merchants', \GD\AdminBundle\Entity\Offer::OFFER_TYPE_CODE_PROMO);
        $codepromoCarousel = array();
        foreach ($codepromoOffers as $o) {
            $codepromoCarousel[$o->getMerchant()->getId()]['merchant_name'] = $o->getMerchant()->getName();
            $codepromoCarousel[$o->getMerchant()->getId()]['merchant_image'] = $o->getMerchant()->getImage();
            $codepromoCarousel[$o->getMerchant()->getId()]['merchant_slug'] = $o->getMerchant()->getSlug();
            if (isset($codepromoCarousel[$o->getMerchant()->getId()]['codepromo_count'])) {
                $codepromoCarousel[$o->getMerchant()->getId()]['codepromo_count'] = $codepromoCarousel[$o->getMerchant()->getId()]['codepromo_count'] + 1;
            } else {
                $codepromoCarousel[$o->getMerchant()->getId()]['codepromo_count'] = 1;
            }
            $codepromoCarousel[$o->getMerchant()->getId()]['merchant_id'] = $o->getMerchant()->getId();
        }

        return array('cashback' => $cashbackCarousel, 'codepromo' => $codepromoCarousel);
    }
    
    /**
     * The specfic header details required for registered and non-registered sections of the website.
     * 
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response 
     */
    public function headerAction()
    {
        $flagUrl = $this->container->getParameter('flag_url');
        $languages = $this->container->getParameter('languages');
        $country = $this->container->getParameter('country_code');
        $accountSummary = null;
        if($this->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->get('security.context')->getToken()->getUser();
            $accountSummary = \GD\SiteBundle\Controller\UserController::getAccountSummary($user);
        }
        
        return $this->render('GDSiteBundle::header.html.twig', array('country_flag' => $flagUrl, 'languages' => $languages, 'country' => $country, 'accountSummary' => $accountSummary));
    }
    
    /**
     * @Route("/newsletter/", name="gd_site_newsletter")
     * @return array
     */
    public function newsletterSubscriptionAction(Request $request, $isNewsletterAjax = false , $isNewsletterfooter = false)
    {
      
      $newsletter = new Newsletter();
      $em = $this->getDoctrine()->getEntityManager();
      $form = $this->createForm(new \GD\SiteBundle\Form\Type\NewsletterType($newsletter));
      $request = $this->get('request');
      $form->setData($newsletter);
      
      if ('POST' == $request->getMethod()) {
        $form->bindRequest($request);

        if($form->isValid()){
          $postedData = $request->request->get('newsletter');
          $email = $postedData['email'];
          $userManager = $this->get('fos_user.user_manager');
          $user = $userManager->findUserByEmail($email);
            //If the user does not have registered account with us
            if(!$user){
                $subscribedUsers = $em->getRepository('GDAdminBundle:Newsletter')->getSubscribedNewsletterUsers($email);
                              
                if($subscribedUsers){
                    $msg = $this->get('translator')->trans('newsletter.subscribed',array(), 'newsletter');
                }else{
                    
                    $newsletter->setEmail($email);
                    $newsletter->setIsSubscribed(true);
                    $em->persist($newsletter);
                    $em->flush();
                     
                    $msg = $this->get('translator')->trans('newsletter.subscription.success',array(), 'newsletter');
                }
            }
            // The user is registered with us and we need to check his/her subscription to newsletter
            else{
                if($user->getNewsletterSubscription()){
                    $msg = $this->get('translator')->trans('newsletter.subscribed',array(), 'newsletter');
                }else{
                    $user->setNewsletterSubscription(1);                    
                    $em->flush();                    
                    $msg = $this->get('translator')->trans('newsletter.subscription.success',array(), 'newsletter');
                }                
            }
             $this->get('session')->setFlash('newsletter_flash', $msg);
            //Notification message
            if($this->getRequest()->isXmlHttpRequest() || $isNewsletterAjax){
              
                return $this->container->get('templating')->renderResponse('GDSiteBundle:Default:newsletter_ajax_success.html.twig',array('msg' => $msg));
            }
         }
         else {
                 if ($request->request->get('newsfooter') == 1)
                 {
                 return $this->render('GDSiteBundle:Default:_newsletter_footer.html.twig', array('form' => $form->createView()));
                 }
         }
       }
       
       if($isNewsletterfooter){
         return $this->render('GDSiteBundle:Default:_newsletter_footer.html.twig', array('form' => $form->createView()));
       }
         
       if($this->getRequest()->isXmlHttpRequest() || $isNewsletterAjax){
                return $this->render('GDSiteBundle:Default:_newsletter_popup.html.twig', array('form' => $form->createView()));
       }
      return $this->render('GDSiteBundle:Default:newsletter.html.twig', array('form' => $form->createView()));
    }
    
    /**
     *  This function just renders the rapid subscription form in the HomePage.
     * 
     * @Route("/rapid-subscription", name="gd_site_user_homepage_registration")
     * @Template()
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $isAjax
     * @return array
     */
    public function rapidSubscriptionAction(Request $request, $isAjax = false)
    {
        $user = new User();
        $form = $this->createForm(new RegistrationType($this->get('translator')), $user);

        return $this->render('GDSiteBundle:Registration:homepage_register_ajax.html.twig', array('form' => $form->createView()));
    }
    
}
