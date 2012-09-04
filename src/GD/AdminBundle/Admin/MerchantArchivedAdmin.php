<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for Archived Merchants (Merchant Entity).
 *  Only those records are fetched that have the isArchived flag set as true. 
 */
class MerchantArchivedAdmin extends SonataAdmin
{
    protected $baseRouteName = 'merchants_archived';
    protected $baseRoutePattern = 'merchants/archived';
    //  $isUpdateAction and $tagId are used with MerchantAdmin only, but restored here to avoid code duplication
    protected $isUpdateAction = false; 
    protected $tagId;
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('duplicate', $this->getRouterIdParameter().'/duplicate');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     *
     *  The fields used to edit the entity
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array('read_only' => true))
            ->add('title', null, array('read_only' => true))
            ->add('description', null, array('attr' => array('class' => 'tinymce'), 'required' => false, 'read_only' => true))
            ->add('slug', null, array('required' => false, 'help' => 'Please leave this field blank if creating a new object'))
            ->add('keywords', null, array('help' => 'Comma separated list of keywords. Example: Tennis Shoes, Running shoes', 'read_only' => true))
            ->add('merchantImage', 'file', array('help' => 'Maximum file size is 1 MB, permissible image types: .png, .jpg and .gif', 'required' => false, 'read_only' => true))
            ->add('defaultAverageFeedback', null, array('read_only' => true))
            ->add('isActive', null, array('read_only' => true))
            ->add('affiliatePartner', null, array('read_only' => true))
            ->add('primaryTag', 'sonata_type_model', array('attr' => array('class' => 'chzn-select'), 'read_only' => true))
            ->add('primaryCategory', 'sonata_type_model', array('attr' => array('class' => 'chzn-select'), 'read_only' => true))
            ->add('categories', 'sonata_type_model', array('expanded' => false, 'multiple' => true, 'attr' => array('class' => 'chzn-select'), 'read_only' => true))
            ->add('tags', 'sonata_type_model', array('expanded' => false, 'multiple' => true, 'attr' => array('class' => 'chzn-select'), 'read_only' => true))
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
            ->add('title')
            ->add('isArchived');
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
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('isActive')
            ->add('affiliatePartner')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('isArchived')
            ->add('archivedAt')
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
            ->end()
            ->with('title')
            ->assertMaxLength(array('limit' => 32))
            ->end();
    }

    /*
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:CRUD:archive_duplicate.html.twig';
    }
    
    /*
     *  Custom query to filter the records 
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'm');
        $query->where('m.isArchived = true');

        return $query;
    }

    /**
     * Returns the list of batchs actions
     *
     * @return array the list of batchs actions
     */
    public function getBatchActions()
    {
        return array();
    }
    
    public function getIsUpdateAction()
    {
        return $this->isUpdateAction;
    }
    public function setIsUpdateAction($isUpdateAction)
    {
        $this->isUpdateAction = $isUpdateAction;
    }
}
