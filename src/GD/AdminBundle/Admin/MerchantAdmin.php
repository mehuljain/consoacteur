<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for the Merchant entity
 *  Only those records are fetched that have the isArchived flag set as false. 
 */
class MerchantAdmin extends SonataAdmin
{
    protected $tagId;
    protected $isUpdateAction = false; //Set to true on update

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('archive', $this->getRouterIdParameter() . '/archive');
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
            ->add('name', null, array('attr' => array('class' => 'translatable')))
            ->add('title', null, array('attr' => array('class' => 'translatable')))
            ->add('metaKeywords', null, array('attr' => array('class' => 'translatable')))
            ->add('metaDescription', null, array('attr' => array('class' => 'translatable')))
            ->add('description', null, array('attr' => array('class' => 'tinymce translatable'), 'required' => false))
            ->add('slug', null, array('required' => false, 'help' => $this->translator->trans('slug.emptynotification.message')))
            ->add('keywords', null, array('help' => $this->translator->trans('merchantkeywords.notification.message'), 'attr' => array('class' => 'translatable')))
            ->add('merchantImage', 'file', array('help' => $this->translator->trans('merchantimage.notification.message'), 'required' => false))
            ->add('defaultAverageFeedback')
            ->add('offerMaturityPeriod', null, array('help' => $this->translator->trans('merchant.offermaturitynotification.message')))
            ->add('isActive')
            ;
         if($this->getIsUpdateAction()){
            $formMapper->add('affiliatePartner', null, array('read_only' => true));
         } else {
            $formMapper->add('affiliatePartner', null);
         }

        $formMapper    
            ->add('primaryTag', 'sonata_type_model', array('attr' => array('class' => 'chzn-select')))
            ->add('primaryCategory', 'sonata_type_model', array('attr' => array('class' => 'chzn-select')))
            ->add('categories', 'sonata_type_model', array('help' => $this->translator->trans('merchant.categorynotification.message') ,'required'=> false, 'expanded' => false, 'multiple' => true, 'attr' => array('class' => 'chzn-select')))
            ->add('tags', 'sonata_type_model', array('help' => $this->translator->trans('merchant.tagnotification.message'),'required'=> false, 'expanded' => false, 'multiple' => true, 'attr' => array('class' => 'chzn-select')));
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

    /*
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:Merchant:edit.html.twig';
    }

    /*
     *  Custom query to filter the records 
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'm');
        $query->where('m.isArchived = false');

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

    public function setIsUpdateAction($isUpdateAction)
    {
        $this->isUpdateAction = $isUpdateAction;
    }

    public function getIsUpdateAction()
    {
        return $this->isUpdateAction;
    }
}
