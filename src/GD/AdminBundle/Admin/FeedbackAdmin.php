<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for the Feedback entity.
 *  The admin needs to approve the Feedback before it is displayed in the frontend. 
 */
class FeedbackAdmin extends SonataAdmin
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
            ->add('merchant', null, array('read_only' => true))
//            ->add('user', null, array('read_only' => true))
            ->add('rating', null, array('read_only' => true))
            ->add('comment')
            ->add('ipAddress', null, array('read_only' => true))
            ->add('isApproved', null, array(
                'required' => false,
                'attr' => array('class' => 'is-approved'),
            ))
            ->add('isRejected', null, array(
                'required' => false,
                'attr' => array('class' => 'is-rejected'),
            ));
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
            ->add('isApproved')
            ->add('isRejected')
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
            ->addIdentifier('id')
            ->addIdentifier('merchant')
            ->addIdentifier('user')
            ->add('isApproved')
            ->add('isRejected')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

}
