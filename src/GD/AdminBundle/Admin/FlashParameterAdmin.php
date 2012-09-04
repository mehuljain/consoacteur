<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use GD\AdminBundle\Entity\Parameter;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for Parameter entity
 *  It provides a generic way to create XML definitions and save it in an XML file named flash.xml. 
 *  It was originally prepared to pass parameters for the Flash menu. 
 *  The service definition for this admin has been removed from services.yml
 *
 *  The fields comprise of:
 *  collectionName: Name of the collection
 *  parameterKey: The key of the XML paramater
 *  parameterValue: The value of the XML parameter
 *
 *  On submiting the form, an XML will be created with the specified Collections, Keys and Values. 
 *  This XML is saved at from GreatDeals/web/flash.xml
 *  Note: The flash.xml file shall be overwritten everytime the form is successfully submited.
 */
class FlashParameterAdmin extends SonataAdmin
{
    protected $baseRouteName = 'flash_parameters';
    protected $baseRoutePattern = 'parameters/flash';
    protected $collectionName = 'flash';

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     *
     *  The fields used to edit the entity
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('collectionName', null, array('attr' => array('readonly' => true, 'value' => $this->collectionName)))
            ->add('parameterKey')
            ->add('parameterValue')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     * @return void
     *
     *  The fields displayed in the list table
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('parameterKey')
            ->add('parameterValue')
        ;
    }

    
    /*
     *  Custom query to filter the records 
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'o');
        $query->where('o.collectionName = :flashName');
        $query->setParameter('flashName', $this->collectionName);

        return $query;
    }

    public function postUpdate($object)
    {
        $this->updateXML();
    }

    public function postPersist($object)
    {
        $this->updateXML();
    }

    public function postRemove($object)
    {
        $this->updateXML();
    }

    private function updateXML()
    {
        $parameters = $this->getModelManager()->findBy($this->getClass(), array('collectionName' => $this->collectionName));

        $xml = new \SimpleXMLElement('<xml/>');

        foreach ($parameters as $p) {
            $parameter = $xml->addChild('parameter');
            $parameter->addChild('key', $p->getParameterKey());
            $parameter->addChild('value', $p->getParameterValue());
        }

        $handle = fopen(__DIR__.'/../../../../web/flash.xml', 'x');
        fwrite($handle, $xml->asXML());
    }
}
