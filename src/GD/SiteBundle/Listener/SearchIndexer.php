<?php

namespace GD\SiteBundle\Listener;

/**
 * Description of SearchIndexer : This class is used to create and 
 * update the Search Index.
 *
 * @author mehul
 */
use Doctrine\ORM\Event\LifecycleEventArgs;
use GD\AdminBundle\Entity\Offer;
use GD\AdminBundle\Entity\Merchant;
use Symfony\Component\DependencyInjection\Container;
use Zend\Search\lib\SearchUtility;

use Gedmo\Mapping\MappedEventSubscriber,
    Gedmo\Translatable\Mapping\Event\TranslatableAdapter,
    Gedmo\Translatable\TranslationListener;

class SearchIndexer extends MappedEventSubscriber {

  protected $router;
  protected $locale;
  public    $date;
  protected $searchUtility;

  /**
   * The constructor is used to initialize the router object 
   * to get the locale.
   *
   * @param \Symfony\Component\Routing\Router $router 
   */
  public function __construct(\Symfony\Component\Routing\Router $router) {
    $this->router = $router;
    $this->date = new \DateTime("now");   
  }
  
  /**
   *  Setter injection method called from search_insert and search_update services from
   *  services.yml file.
   * 
   * @param \Zend\Search\lib\SearchUtility $searchUtility 
   */
  public function setSearchUtility(\Zend\Search\lib\SearchUtility $searchUtility){
    $this->searchUtility = $searchUtility;
  }

  /**
   * Specifies the list of events to listen
   *
   * @return array
   */
  public function getSubscribedEvents() {
    return array(
        'postPersist',
        'onFlush',
        'postUpdate',
    );
  }

  /**
   * {@inheritDoc}
   */
  protected function getNamespace() {
    return __NAMESPACE__;
  }

  /**
   * This method is used to index Cashback offers
   * on doctrine inserts
   * 
   * @param LifecycleEventArgs $args 
   */
  public function postPersist(LifecycleEventArgs $args) {
    $entity = $args->getEntity();
    $entityManager = $args->getEntityManager();

    if ($entity instanceof Offer) {
      
      if ($entity->getIsCurrent() && !$entity->getIsArchived() && $entity->getMerchant()->getIsActive() && !$entity->getMerchant()->getIsArchived() && $entity->getStartDate() <= $this->date && $entity->getEndDate() >= $this->date) {
        $gainvalobj = $entityManager->getRepository('GDAdminBundle:Offer')->findOneBy(array('merchant' => $entity->getMerchant()->getId(), 'type' => 1, 'isCurrent' => 1, 'isArchived' => 0));
        $this->setLocale($entity);
        $this->searchUtility->updateLuceneIndex($entity, $this->locale, $gainvalobj);
      }
    }
  }

  /**
   * This method is to update the current index 
   * @param LifecycleEventArgs $args 
   */
  public function postUpdate(LifecycleEventArgs $args) {

    $translatableListener = new TranslationListener();
    $ea = $translatableListener->getEventAdapter($args);
    $object = $ea->getObject();
    
    /* Update of an offer */
    if ($object instanceof Offer) {

      $om = $ea->getObjectManager();
      $gainvalobj = new Offer();
      $meta = $om->getClassMetadata(get_class($object));
      $config = $translatableListener->getConfiguration($om, $meta->name);
      $translationClass = $translatableListener->getTranslationClass($ea, $meta->name);

      if ($object->getIsCurrent() && !$object->getIsArchived() && $object->getMerchant()->getIsActive() && !$object->getMerchant()->getIsArchived() && $object->getStartDate() <= $this->date && $object->getEndDate() >= $this->date) {
        $this->setLocale($object);
        if($object->getType() == 1 ){
          $userGainval = $ea->findTranslation($object->getId(), $meta->name, $this->locale, 'userGainValue', $translationClass);
          if (!empty($userGainval)) {
            $object->setUserGainValue($userGainval->getContent());
          }         
            $gainvalobj = $object;         
        }
        else{          
          $gainvalobj = $om->getRepository('GDAdminBundle:Offer')->findOneBy(array('merchant' => $object->getMerchant()->getId(), 'type' => 1, 'isCurrent' => 1, 'isArchived' => 0));
        }
        $this->searchUtility->updateLuceneIndex($object, $this->locale, $gainvalobj);
      } else if ($object->getIsArchived() || !$object->getIsCurrent()) {
        $this->setLocale($object);
        $gainvalobj = $om->getRepository('GDAdminBundle:Offer')->findOneBy(array('merchant' => $object->getMerchant()->getId(), 'type' => 1, 'isCurrent' => 1, 'isArchived' => 0));
        $otherobj = $om->getRepository('GDAdminBundle:Offer')->findBy(array('merchant' => $object->getMerchant()->getId(), 'isCurrent' => 1, 'isArchived' => 0));
        if(empty($gainvalobj)){
          if(count($otherobj)){
            $this->searchUtility->updateLuceneIndex($object, $this->locale,$gainvalobj);
          }
          else{
            $this->searchUtility->deleteDocument($object, $this->locale);
          }
        }
      } else if($object->getStartDate() < $this->date && $object->getEndDate() < $this->date){
        $this->setLocale($object);
        $this->searchUtility->deleteDocument($object, $this->locale);
      }
    }

    /* Update of a merchant */
    if ($object instanceof Merchant) {

      if ($object->getIsActive() && !$object->getIsArchived()) {

        $om = $ea->getObjectManager();
        $meta = $om->getClassMetadata(get_class($object));
        $config = $translatableListener->getConfiguration($om, $meta->name);
        $translationClass = $translatableListener->getTranslationClass($ea, $meta->name);
        $this->setLocale($object);
        $keywordval = $ea->findTranslation($object->getId(), $meta->name, $this->locale, 'keywords', $translationClass);
        $nameval = $ea->findTranslation($object->getId(), $meta->name, $this->locale, 'name', $translationClass);
        if (!empty($keywordval)) {
          $object->setKeywords($keywordval->getContent());
        }
        if (!empty($nameval)) {
          $object->setName($nameval->getContent());
        }
        
        $obj = $om->getRepository('GDAdminBundle:Offer')->findOneBy(array('merchant' => $object->getId(), 'type' => 1));
        $gainvalobj = $obj;
        if (!empty($obj)) {
          $this->setLocale($object);
          $this->searchUtility->updateLuceneIndex($obj, $this->locale,$gainvalobj);
        }
      } else if (!$object->getIsActive() || $object->getIsArchived()) {
        $om = $ea->getObjectManager();
        $obj = $om->getRepository('GDAdminBundle:Offer')->findOneBy(array('merchant' => $object->getId(), 'type' => 1));
        if (!empty($obj)) {
          $this->setLocale($object);
          $this->searchUtility->deleteDocument($obj, $this->locale);
        }
      }
    }
  }

  /**
   * Sets the locale value to update the index
   * @param type $entity 
   */
  public function setLocale($entity) {
    $this->locale = $entity->getTranslatableLocale();
    $this->locale = (empty($this->locale)) ? $this->router->getContext()->getParameter('_locale') : $this->locale;
  }

}
