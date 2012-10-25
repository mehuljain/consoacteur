<?php

namespace GD\AdminBundle\Admin\Sonata;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use FOS\UserBundle\Model\UserManagerInterface;

/*
 *  This class extends the Admin class of Sonata Bundle - Sonata\AdminBundle\Admin\Admin
 *  The methods defined here will override the default implementations in Sonata
 *  
 *  The admin class is a service implementing the AdminInterface interface
 *  The following required dependencies are automatically injected:-
 *  ListBuilder: builds the list fields
 *  FormContractor: builds the form using the Symfony FormBuilder
 *  DatagridBuilder: builds the filter fields
 *  Router: generates the different urls
 *  Request
 *  ModelManager: Service which handles specific ORM code
 *  Translator
 */
class SonataAdmin extends Admin
{
    /**
     * The number of results to display in the list view
     *
     * @var integer
     */
    protected $maxPerPage = 50;
    
    /**
     * The format in which entries can be downloaded
     */
    public function getExportFormats()
    {
        return array(
            'xls','csv'
        );
    }

    /**
     * Returns the list of batchs actions
     * Delete this entire function to get Batch Delete action
     * @return array the list of batchs actions
     */
    public function getBatchActions()
    {
        $actions = array();
        return $actions;
    }

    /**
     * {@inheritdoc}
     * This method overrides the trans method that comes with Sonata. 
     * It is used for customizing the Base Translation behavior
     *
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $domain = $domain ?: $this->translationDomain;

        if (!$this->translator) {
            return $id;
        }

        $language = $this->getRequest()->getSession()->get('base_language');
        //$this->translator->setLocale($language);

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
    */

    /**
     * translate a message id
     *
     * This method overrides the trans function that come with Sonata. 
     * It is used for customizing the Base Translation behavior
     *
     * @param string  $id
     * @param integer $count
     * @param array   $parameters
     * @param null    $domain
     * @param null    $locale
     *
     * @return string the translated string
     *
    public function transChoice($id, $count, array $parameters = array(), $domain = null, $locale = null)
    {
        $domain = $domain ?: $this->translationDomain;

        if (!$this->translator) {
            return $id;
        }

        $language = $this->getRequest()->getSession()->get('base_language');
        //$this->translator->setLocale($language);

        return $this->translator->transChoice($id, $count, $parameters, $domain, $locale);
    } 
    */

}