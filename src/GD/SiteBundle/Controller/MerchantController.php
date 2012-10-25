<?php

namespace GD\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GD\AdminBundle\Entity\Offer;
use GD\AdminBundle\Entity\OfferUsage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use GD\AdminBundle\Entity\Merchant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MerchantController extends Controller
{
    const BREADCRUMB_NEW_OFFERS = "new.offers";
    const BREADCRUMB_TOP_OFFERS = "top.offers";
    const BREADCRUMB_PRIVATE_OFFERS = "private.offers";
    const BREADCRUMB_PREFERRED_OFFERS = "preferred.offers";
    const BREADCRUMB_CASHBACK = "cashback.offer";
    const BREADCRUMB_CODE_PROMO = "code.promo";
    const BREADCRUMB_FULL_REIMBRUISEMENT = "full.reimbursement";
    const BREADCRUMB_SUBSCRIPTION_GAIN = "subscription.gain";
    
    /**
     *  This function creates breadcrumbs links for the merchant show page
     * 
     * @param array $merchant  - The merchant object
     */

    private function createMerchantDetailsBreadcrumbs($merchant)
    {
        if(!empty($merchant)){
          $breadcrumbs = $this->get("white_october_breadcrumbs");
          $breadcrumbs->addItem($this->get('translator')->trans('home.page', array(), 'breadcrumb'), $this->get("router")->generate("gd_home"));

          $primaryCategory = $merchant->getPrimaryCategory();
          $primaryTag = $merchant->getPrimaryTag();
          if(!empty($primaryCategory) && !empty($primaryTag)){
            $breadcrumbs->addItem($this->get('translator')->trans($primaryCategory->getSlug(), array(), 'breadcrumb'), $this->get("router")->generate("gd_merchant_list", array('filter' => 'category', 'type' => $primaryCategory->getSlug())));
            $breadcrumbs->addItem($this->get('translator')->trans($primaryTag->getSlug(), array(), 'breadcrumb'), $this->get("router")->generate("gd_merchant_list", array('filter' => 'tag', 'type' => $primaryTag->getSlug())));
            $breadcrumbs->addItem($this->get('translator')->trans($merchant->getSlug(), array(), 'breadcrumb'), $this->get("router")->generate("gd_merchant_show", array('slug' => $merchant->getSlug())));
          }
        }
    }

    /**
     * The listing of merchants based on the type of offer (cashback, code promo...)
     * 
     * @Route("/merchants/{filter}/{type}/page/{page}/rows-per-page/{rows_per_page}/sort-by/{sort_by}/search-key/{search_key}", name="gd_merchant_list", requirements={"filters" = "offer|category|tag|search"}, defaults={"filter" = "offer", "page" = 1, "rows_per_page" = 20, "sort_by" = "name" ,"search_key" = ""})
     * @Template()
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function listAction(Request $request)
    {
        $session = $this->get('Session');
        if ('category' == $request->get('filter') || 'tag' == $request->get('filter')){
            $session->set('menu', $request->get('filter'));
        }else{
            $session->set('menu', $request->get('type'));
        }
        
        $this->createListBreadcrumbs($request->get('type'),$request->get('filter'),$request->get('primarycategory'));

        $user = $this->get('security.context')->getToken()->getUser();

        $searchKey = strlen($request->get('search_key')) ? $request->get('search_key') : null;

        $query = $this->getDoctrine()->getEntityManager()->getRepository('GDAdminBundle:Merchant')
            ->getActiveMerchantsQuery($request->get('filter'), $request->get('type'), $searchKey, $request->get('sort_by'), $user);
        $result = $query->getResult();

        $totalOffers = count($result);

        $paginator = $this->get('knp_paginator');
        $merchantsPagination = $paginator->paginate(
            $result,
            $this->get('request')->get('page', 1)/*page number*/,
            $this->get('request')->get('rows_per_page', 20)/*limit per page*/
        );

        //pagination parameters for template
        $merchantsPagination->setParam('type', $this->get('request')->get('type'));
        $merchantsPagination->setParam('rows_per_page', $this->get('request')->get('rows_per_page'));

        $availableMerchantsCharacterMap = $this->getDoctrine()->getEntityManager()
            ->getRepository('GDAdminBundle:Merchant')->getActiveMerchantsCharacterMap($this->get('request')->get('filter'), $this->get('request')->get('type'), $user);

        return array('merchantsPagination' => $merchantsPagination,
                     'availableMerchantsCharacterMap'=> $availableMerchantsCharacterMap,
                     'totalOffers' => $totalOffers);
    }

    /**
     * Breadcrumb links for the merchant listing page.
     *
     * @param string $type The type of offer
     * @param string $filter The filter (category or tag)
     * @param string $primarycategory  The primarycategory in case tag is passed as the filter
     */
    private function createListBreadcrumbs($type,$filter,$primarycategory = NULL)
    {
        switch($type){
            case 'new':                $type = self::BREADCRUMB_NEW_OFFERS; break;
            case 'top':                $type = self::BREADCRUMB_TOP_OFFERS; break;
            case 'private':            $type = self::BREADCRUMB_PRIVATE_OFFERS; break;
            case 'preferred':          $type = self::BREADCRUMB_PREFERRED_OFFERS; break;
            case 'cashback':           $type = self::BREADCRUMB_CASHBACK; break;
            case 'full-reimbursement': $type = self::BREADCRUMB_FULL_REIMBRUISEMENT; break;
            case 'code-promo':         $type = self::BREADCRUMB_CODE_PROMO; break;
            case 'subscription-gain':  $type = self::BREADCRUMB_SUBSCRIPTION_GAIN; break;
            default: 
              $type = $type;
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($this->get('translator')->trans('home.page', array(), 'breadcrumb'), $this->get("router")->generate("gd_home"));
        if($filter == 'tag' ){
           $breadcrumbs->addItem($this->get('translator')->trans($primarycategory, array(), 'breadcrumb'), $this->get("router")->generate("gd_merchant_list", array('filter' => 'category', 'type' => $primarycategory)));         
        }        
        $breadcrumbs->addItem($this->get('translator')->trans($type, array(), 'breadcrumb'), $this->get("router")->generate("gd_merchant_list", array('type' => $type, 'filter' => $filter)));
    }

    /**
     *  This function is used to display the offer details of a merchant
     * 
     * @Route("/merchants/{slug}/{page}", name="gd_merchant_show", defaults = { "page" = 1 })
     * @ParamConverter("slug", class="GDAdminBundle:Merchant")
     * @Template()
     * @param \GD\AdminBundle\Entity\Merchant $merchant
     * @return array
     */
    public function showAction(Merchant $merchant)
    {
        $this->createMerchantDetailsBreadcrumbs($merchant);
        $validFeedbacks = $this->getDoctrine()->getRepository('GDAdminBundle:Feedback')->getMerchantFeedbacks($merchant);

        $feedbackPaginator = $this->get('knp_paginator');
        $feedbackPagination = $feedbackPaginator->paginate(
            $validFeedbacks,
            $this->get('request')->get('page', 1)/*page number*/,
            5 /*limit per page*/
        );

        if ($this->get('request')->isXmlHttpRequest()) {
            return $this->render('GDSiteBundle:Merchant:_feedback.html.twig', array('feedbackPagination' => $feedbackPagination));
        }

        $offers = $this->getDoctrine()->getRepository('GDAdminBundle:Offer')->getActiveOffersByMerchant($merchant);

        $cashbackOffer = null;
        $fullReimbursementOffer = null;
        $subscriptionGain = null;
        $codePromoOffers = array();
        $today = new \DateTime('today');
        foreach ($offers as $o) {
            if($o->getIsCurrent() && !$o->getIsArchived() && $o->getStartDate() <= $today && $o->getEndDate() >= $today) {
                if ($o->getType() === Offer::OFFER_TYPE_CASHBACK) {
                    $cashbackOffer = $o;
                } else if ($o->getType() === Offer::OFFER_TYPE_FULL_REIMBURSEMENT) {
                    $fullReimbursementOffer = $o;
                } else if ($o->getType() === Offer::OFFER_TYPE_SUBSCRIPTION_GAIN) {
                    $subscriptionGain = $o;
                } else if ($o->getType() === Offer::OFFER_TYPE_CODE_PROMO) {
                    $codePromoOffers[] = $o;
                }
            }
        }

        $numFeedback = sizeof($validFeedbacks);
        $sumFeedback = 0;
        foreach($validFeedbacks as $feedback) {
            $sumFeedback += $feedback->getRating(); 
        }
        
        $i = ($numFeedback == 0)? $merchant->getDefaultAverageFeedback() : $sumFeedback/$numFeedback;
        $starRating = array('full' => 0, 'half' => 0, 'quarter' => 0, 'quarter_and_half' => 0, 'empty' => 5);
        while ($i > 0) {
            if ($i < 1) {
                if ($i >= 0.25 && $i < 0.50) {
                    $starRating['quarter'] += 1;
                    $starRating['empty'] -= 1;
                } elseif ($i >= 0.50 && $i < 0.75) {
                    $starRating['half'] += 1;
                    $starRating['empty'] -= 1;
                } elseif ($i >= 0.75 && $i < 1) {
                    $starRating['quarter_and_half'] += 1;
                    $starRating['empty'] -= 1;
                }
            } else {
                $starRating['full'] += 1;
                $starRating['empty'] -= 1;
            }
            $i--;
        }

        return array('merchant' => $merchant, 
                     'cashbackOffer' => $cashbackOffer,
                     'fullReimbursementOffer' => $fullReimbursementOffer,
                     'subscriptionGain' => $subscriptionGain,
                     'codePromoOffers' => $codePromoOffers,
                     'feedbackPagination' => $feedbackPagination,
                     'starRating' => $starRating,
                     'numFeedback' => $numFeedback);
    }

    /**
     * This function logs the user offer clicks in the database and also handles 
     * replacement of the string "{userId}" in redirection url before redirecting
     * to the merchant's website.     
     * 
     * @Route("/offers/redirect-to-vendor/{slug}", name="gd_offers_usage")
     * @Template()
     * @param \GD\AdminBundle\Entity\Offer $offer
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function offersUsageAction(Offer $offer, Request $request)
    {
        $redirectionUri = $offer->getRedirectionUri();
        
        $usage = new OfferUsage();
        $usage->setClickTime(new \DateTime());
        if($this->get('security.context')->isGranted('ROLE_USER')){
            $usage->setUser($this->get('security.context')->getToken()->getUser());
            $trackingId = $this->get('security.context')->getToken()->getUser()->getId();
        }else{            
            $defaultUser = $this->container->getParameter('default_user');
            $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($defaultUser);
        
            if (null === $user) {
              //if default is user not created, we will provide another default value of 1
              $trackingId = 1;
            }
            else {
              $trackingId = $user->getId();
            }
            $usage->setUser(null);
            $redirectionDeal = preg_replace('/{\s*userid\s*}/i', $trackingId, $redirectionUri);
            return $this->render('GDSiteBundle:Merchant:redirectionValidate.html.twig', array( 'redirectionDeal'=> $redirectionDeal,'redirectionSansDeal' => $redirectionUri));            
        }
        $usage->setIp($request->getClientIp());
        $usage->setOffer($offer);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($usage);
        $em->flush();
        //The redirection url must append only the user id...
        $redirectionDeal = preg_replace('/{\s*userid\s*}/i', $trackingId, $redirectionUri);
        return new RedirectResponse($redirectionDeal);
    }
}
