<?php

namespace Zend\Search\lib;

use Symfony\Component\HttpFoundation\Request;
use Zend\Search\Lucene\Lucene as ZendLucene;
use Zend\Search\Lucene\Document as ZendDocument;
use Zend\Search\Lucene\Document\Field as ZendField;
use Zend\Search\Lucene\Analysis\Analyzer\Analyzer;
use Zend\Search\Lucene\Analysis\Analyzer\Common\MyAnalyzer\CaseInsensitive;
use Zend\Search\Lucene\Index\Term;
use GD\AdminBundle\Entity\Merchant;
use GD\AdminBundle\Entity\Offer;

/**
 * Description of SearchUtility: This class provides the necessary utility functions to create and 
 * update the Lucene Index.
 *
 * @author mehul
 */
class SearchUtility {

  protected $doc;
  protected $index;
  protected $merchantId;
  protected $name;
  protected $keywords;
  protected $userGainValue;
  protected $merchantSlug;
  
  /**
   * Constructor 
   */
  public function __construct(){
    
    Analyzer::setDefault(new CaseInsensitive());    
     
  }

  /**
   * For all updates made to Lucene Index file this function is called. It internally
   * calls to delete the old indexed records and updates the file by inserting the updated
   * record.
   *
   * @param Offer  $object - The Offer entity object
   * @param string $locale - The locale in which the offer is to be updated. 
   * @param type $gainvalobj - This is used for the userGainValue of the Cashback offers.
   */

  public function updateLuceneIndex(Offer $object, $locale, $gainvalobj) {

    $this->index = $this->getLuceneIndex($locale);

    $this->deleteDocument($object, $locale);

    $this->createDocument($object, $gainvalobj);

    $this->index->addDocument($this->doc);

    $this->commitIndex();
  }
  
  /**
   * This method creates the Lucene index file if one does not exist else opens the 
   * existing one for the respective locale.
   * 
   * @param string $locale - The locale
   * @return file Lucene index file 
   */

  public function getLuceneIndex($locale) {

    if (file_exists($this->index = $this->getLuceneIndexFile($locale))) {
      return ZendLucene::open($this->index);
    } else {
      return ZendLucene::create($this->index);
    }
  }

  /**
   * Returns the path of the existing Lucene index file for the 
   * country locale combination.
   *
   * @param string $locale
   * @return string - The path to the index file 
   */
  public function getLuceneIndexFile($locale) {

    $req = Request::createFromGlobals();
    return __DIR__ . '/../../../../app/cache/data/index-' . $req->server->get('SYMFONY__DOMAIN__NAME') . '-' . $locale;
  }

  /**
   * Deletes a indexed record from the Lucene index file 
   * using the primary key.
   * 
   * @param Offer $object - The offer entity object
   * @param string $locale 
   */
  public function deleteDocument(Offer $object, $locale) {

    $term = new Term($object->getMerchant()->getId(), 'merchantId');

    if (empty($this->index)) {
      $this->index = $this->getLuceneIndex($locale);
    }

    $docIds = $this->index->termDocs($term);

    foreach ($docIds as $id) {
      $this->index->delete($id);
    }

    $this->commitIndex();
  }
  
  /**
   * Commits and optimizes the Lucene Index file.
   * See Zend Lucene Documentation for more information. 
   */

  public function commitIndex() {
    $this->index->commit();
    $this->index->optimize();
  }
  /**
   * Creates a index record in the index file.
   * 
   * @param Offer $object - The entity Offer object 
   * @param Offer $gainvalobj - The Offer object carrying the usergain value for the Cashback offer
   * as we display only that in the Search results.
   */
  public function createDocument(Offer $object, $gainvalobj) {

    $this->setDocumentParameters($object, $gainvalobj);

    $this->doc = new ZendDocument();

    // store merchant primary key to identify it in the search results
    $this->doc->addField(ZendField::keyword('merchantId', $this->merchantId, 'utf-8'));
    $this->doc->addField(ZendField::unIndexed('merchantSlug', $this->merchantSlug, 'utf-8'));
    $this->doc->addField(ZendField::text('companyName', $this->name, 'utf-8'));
    $this->doc->addField(ZendField::unIndexed('cashBack', $this->userGainValue, 'utf-8'));
    if (!empty($this->keywords)) {
      $str = array_map('trim', explode(",", $this->keywords));
      $i = 0;
      foreach ($str as $value) {
        $i++;
        $this->doc->addField(ZendField::text("$i", $value, 'utf-8'));
      }
    }
  }

  /**
   *
   * @param Offer $object - The entity Offer object
   * @param Offer $gainvalobj - The Offer object carrying the usergain value 
   */
  public function setDocumentParameters(Offer $object, $gainvalobj) {

    $this->name = $object->getMerchant()->getName();
    $this->merchantId = $object->getMerchant()->getId();
    $this->merchantSlug = $object->getMerchant()->getSlug();
    $this->keywords = $object->getMerchant()->getKeywords();
    $this->userGainValue = '';
    if (!empty($gainvalobj)) {
      $this->userGainValue = $gainvalobj->getUserGainValue();
    }
  }

  /**
   *
   * @param string $name - The name to convert.
   * @return string - The converted string. 
   */
  static public function replaceSpecialAlphabets($name) {

    $search = array('á', 'à', 'å', 'æ', 'ä', 'â', 'ą', 'ã',
        'č', 'ç',
        'ď',
        'é', 'è', 'ê', 'ë', 'ě',
        'ğ',
        'í', 'ï', 'î', 'ì',
        'ł', 'ĺ', 'ľ',
        'ň', 'ń', 'ñ',
        'ó', 'ö', 'œ', 'ő', 'ò', 'ô', 'õ', 'ø',
        'ř', 'ŕ',
        'š', 'ś', 'ş', 'ș',
        'ť', 'ţ', 'ț',
        'ú', 'ů', 'ü', 'ù', 'û', 'ű',
        'ý', 'ÿ',
        'ž', 'ź', 'ż',
        'ß');

    $replace = array('a', 'a', 'a', 'ae', 'a', 'a', 'a', 'a',
        'c', 'c',
        'd',
        'e', 'e', 'e', 'e', 'e',
        'g',
        'i', 'i', 'i', 'i',
        'l', 'l', 'l',
        'n', 'n', 'n',
        'o', 'o', 'oe', 'o', 'o', 'o', 'o', 'o',
        'r', 'r',
        's', 's', 's', 's',
        't', 't', 't',
        'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y',
        'z', 'z', 'z',
        'ss');

    $res = str_replace($search, $replace, $name);
    return $res;
  }

}