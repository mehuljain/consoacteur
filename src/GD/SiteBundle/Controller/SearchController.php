<?php

namespace GD\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Search\Lucene\Index\Term;
use Zend\Search\Lucene\Search\Query\Wildcard;
use Zend\Search\lib\SearchUtility;

class SearchController extends Controller {

  protected $index;

  /**
   * This action will collect the input entered in the frontend 
   * and query the search index using the find method and 
   * return the response.
   * 
   * @param type $_locale
   * @return \Symfony\Component\HttpFoundation\Response 
   */
  public function executeSearchAction($_locale) {

    $searchlib = new SearchUtility();
    $this->index = $searchlib->getLuceneIndex($_locale);
    Wildcard::setMinPrefixLength('1');

    $search = SearchUtility::replaceSpecialAlphabets(mb_strtolower($this->getRequest()->get('term'), 'UTF-8'));

    if (strlen($search) > 0) {
      $search = str_replace(' ', '*', $search);
      $search .= '*';
      $queryStr = new Wildcard(new Term($search));
      $hits = $this->index->find($queryStr);
    }

    if ($this->getRequest()->isXmlHttpRequest()) {
      $data = array();
      $i = 0;
      if (isset($hits)) {
        foreach ($hits as $h) {
          $i++;
          if ($i < 6) {
            $data[] = array('id' => $h->merchantId, 'value' => $h->companyName, 'label' => $h->companyName.' '.$h->cashBack, 'slug' => $h->merchantSlug);
          }
        }
      }
      $response = new Response(json_encode($data));
      $response->headers->set('Content-Type', 'application/json');

      return $response;
    } else {
      /**
       * Note that in this $type will contain the CSV of merchant IDs, which will be used inside the subquery in MerchantRepository
       */
      $type = '';
      if (isset($hits)) {
        foreach ($hits as $h) {
          $type .=!strlen($type) ? '' : ',';
          $type .= $h->merchantId;
        }
      }
      if (!strlen($type)) {
        $type = '0';
      }

      return $this->redirect($this->generateUrl('gd_merchant_list', array('filter' => 'search', 'type' => $type)));
    }
  }

}
