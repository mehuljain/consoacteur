<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use GD\AdminBundle\Entity\MerchantList;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for MerchantList entity.
 *
 *  Merchant List is an abstraction to group a subset of merchants. 
 *  There are two kinds of lists, 'Normal Lists' and 'Carousel Lists'
 *  Only those records are fetched that have type as LIST_TYPE_NORMAL. 
 *  This corresponds to 'new', 'private' and 'top' merchants
 */
class MerchantListAdmin extends SonataAdmin
{
    protected $baseRouteName = 'merchant_normal_list';
    protected $baseRoutePattern = 'merchantlists/normal';

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     *
     *  The fields used to edit the entity
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array('attr' => array('readonly' => true)))
            ->add('merchants', 'sonata_type_model', array('expanded' => false, 'multiple' => true, 'attr' => array('class' => 'chzn-select')))
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     * @return void
     * 
     * The fields available for filtering the list
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
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
            ->addIdentifier('name')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Validator\ErrorElement $errorElement
     * @param $object
     * @return void
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('name')
            ->assertMaxLength(array('limit' => 32))
            ->end();
    }

    /*
     *  Custom query to filter the records 
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'o');
        $query->where('o.type = :normalType');
        $query->setParameter('normalType', MerchantList::LIST_TYPE_NORMAL);

        return $query;
    }
}
