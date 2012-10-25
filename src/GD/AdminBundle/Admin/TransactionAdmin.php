<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use GD\AdminBundle\Entity\Transaction;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for the Transaction  entity
 */
class TransactionAdmin extends SonataAdmin
{
    protected $baseRouteName = 'all_transactions';
    protected $baseRoutePattern = 'transaction';
    protected $isUpdateAction = false;
    protected $isWithdrawalAssigned = false; 

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     *
     *  The fields used to edit the entity
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $customerQuery = $this->getModelManager()->createQuery('GD\AdminBundle\Entity\User', 'u');
        $customerQuery->where('u.isAdminUser = false AND u.isArchived = false AND u.isBlacklisted = false');

        if($this->isUpdateAction || $this->isWithdrawalAssigned) {
            $formMapper
                ->add('user', 'text', array('read_only' => true));
        } else {
            $formMapper
//                ->add('user', 'sonata_type_model', array('query' => $customerQuery, 'attr' => array('class' => 'chzn-select')), array('edit' => 'standard'));
                  ->add('user', 'text', array('read_only' => false));
        }

        if($this->isWithdrawalAssigned) {
            $formMapper
                ->add('userGainAmount', 'money', array('required' => false, 'read_only' => true))
                ->add('commissionAmount', 'money', array('required' => false, 'read_only' => true))
                ->add('transactionAmount', 'money', array('required' => false, 'read_only' => true))
            ;
        } else {
            $formMapper
                ->add('userGainAmount', 'money', array('required' => false))
                ->add('commissionAmount', 'money', array('required' => false))
                ->add('transactionAmount', 'money', array('required' => false))
            ;
        }

        if($this->isUpdateAction || $this->isWithdrawalAssigned) {
            $formMapper
                ->add('type', 'choice', array('choices' => Transaction::getTypeList(), 'read_only' => true));
        } else {
            $formMapper
                ->add('type', 'choice', array('choices' => Transaction::getTypeList()));
        }

        if($this->isWithdrawalAssigned) {
            $formMapper
                ->add('status', 'choice', array('choices' => Transaction::getStatusList(), 'read_only' => true))
                ->add('programId', null, array('read_only' => true))
                ->add('withdrawal', null, array('read_only' => true, 'empty_value' => 'No Withdrawal Assigned')) 
                ->add('transactionDate', null, array('read_only' => true))
                ->add('validationDate', null, array('read_only' => true))
                ->add('confirmationDate', null, array('read_only' => true))
                ->add('merchantConfirmationDate', null, array('read_only' => true))
                ->add('merchantCancellationDate', null, array('read_only' => true))
                ->add('rejectionDate', null, array('read_only' => true))
                ->add('lostDate', null, array('read_only' => true))
                ->add('orderNumber', null, array('read_only' => true))
                ->add('reason', null, array('read_only' => true))
            ;
        } else {
            $formMapper
                ->add('status', 'choice', array('choices' => Transaction::getStatusList()))
                ->add('programId')
                ->add('withdrawal', null, array('read_only' => true, 'empty_value' => 'No Withdrawal Assigned')) 
                ->add('transactionDate')
                ->add('validationDate')
                ->add('confirmationDate')
                ->add('merchantConfirmationDate')
                ->add('merchantCancellationDate')
                ->add('rejectionDate')
                ->add('lostDate')
                ->add('orderNumber')
                ->add('reason')
            ;
        }

        if($this->isUpdateAction || $this->isWithdrawalAssigned) {
            $formMapper
                ->add('createdAt', null, array('read_only' => true))
                ->add('updatedAt', null, array('read_only' => true))
                ;
        }
                // TODO: Read currency parameter from config for money field type
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
            ->add('id')
            ->add('offer')
            ->add('username')
            ->add('status', 'doctrine_orm_choice', array(), 'choice', array('choices' => Transaction::getStatusList()))
            ->add('type', 'doctrine_orm_choice', array(), 'choice', array('choices' => Transaction::getTypeList()))
            ->add('programId')
            ->add('orderNumber')
            ->add('offerType', 'doctrine_orm_choice', array(), 'choice', array('choices' => \GD\AdminBundle\Entity\Offer::getTypeList()))
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
            ->add('user')
            ->add('typeAsString', null, array('sortable' => true, 'label' => 'Type'))
            ->add('statusAsString', null, array('sortable' => true, 'label' => 'Status'))
            ->add('offer')
            ->add('referral')
            ->add('programId')
            ->add('userGainAmount')
            ->add('commissionAmount')
            ->add('transactionAmount')
            ->add('createdAt')
            ->add('transactionDate')
            ->add('confirmationDate')
            ->add('merchantConfirmationDate')
            ->add('rejectionDate')
        ;
    }

    /*
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:Transaction:base_edit.html.twig';
    }

    public function setIsUpdateAction($isUpdateAction)
    {
        $this->isUpdateAction = $isUpdateAction;
    }
    
    public function setIsWithdrawalAssigned($isWithdrawalAssigned)
    {
        $this->isWithdrawalAssigned = $isWithdrawalAssigned;
    }
}
