<?php

namespace GD\AdminBundle\Admin;

//use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for the Tag entity
 */
class TagAdmin extends SonataAdmin
{
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
            ->add('description', null, array('attr' => array('class' => 'translatable')))
            ->add('slug', null, array('required' => false, 'help' => $this->translator->trans('slug.emptynotification.message')))
            ->add('categories', 'sonata_type_model', array('expanded' => false, 'multiple' => true, 'attr' => array('class' => 'chzn-select')));
            
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
            ->add('categories');
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
            ->add('id')
            ->addIdentifier('name');
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
}
