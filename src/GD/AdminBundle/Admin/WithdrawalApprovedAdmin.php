<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use GD\AdminBundle\Entity\Withdrawal;
use GD\AdminBundle\Entity\Transaction;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for the Withdrawal entity with status as TRANSACTION_STATUS_APPROVED
 */
class WithdrawalApprovedAdmin extends SonataAdmin
{
    protected $withdrawalType;
    protected $baseRouteName = 'withdrawal_approved';
    protected $baseRoutePattern = 'withdrawal/approved';
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('paid', $this->getRouterIdParameter().'/paid');
        $collection->add('hold', $this->getRouterIdParameter().'/hold');
    }
    
    public function setWithdrawalType($withdrawalType)
    {
        $this->withdrawalType = $withdrawalType;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     *
     *  The fields used to edit the entity
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('user', 'text', array('read_only' => true));
        switch($this->withdrawalType){
            case(Withdrawal::WITHDRAWAL_TYPE_BANK_1):
                $formMapper
                    ->add('iban', null, array('read_only' => true))
                    ->add('address', null, array('read_only' => true))
                    ->add('swiftCode', null, array('read_only' => true))
                ; 
                break;
            case(Withdrawal::WITHDRAWAL_TYPE_BANK_2):
                $formMapper
                    ->add('bankName', null, array('read_only' => true))
                    ->add('country', null, array('read_only' => true))
                    ->add('address', null, array('read_only' => true))
                    ->add('accountNumber', null, array('read_only' => true))
                ;
                break;
            case(Withdrawal::WITHDRAWAL_TYPE_CHEQUE):
                $formMapper->add('chequePayee', null, array('read_only' => true));
                $formMapper->add('address', null, array('read_only' => true));
                break;
            case(Withdrawal::WITHDRAWAL_TYPE_PAYPAL):
                $formMapper->add('email', null, array('read_only' => true));
                $formMapper->add('address', null, array('read_only' => true));
                break;
        }
        $formMapper->add('amount', null, array('read_only' => true));
        $formMapper->add('code', null, array('read_only' => true));
        $formMapper->add('internalComment');
        $formMapper->add('userComment');
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
            ->add('username')
            ->add('status', 'doctrine_orm_choice', array(), 'choice', array('choices' => Transaction::getStatusList()))
            ->add('type', 'doctrine_orm_choice', array(), 'choice', array('choices' => Withdrawal::getWithdrawalTypeList()))
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
            ->add('statusAsString', null, array('sortable' => true, 'label' => 'Status'))
            ->add('typeAsString', null, array('sortable' => true, 'label' => 'Type'))
            ->add('createdAt')
            ->add('updatedAt')
            ->add('validatedAt')
            ->add('requestedAt')
            ->add('processedAt')
            ->add('paidAt')
        ;
    }

    /*
     *  Custom query to filter the records 
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'o');
        $query->where('o.status = '.Transaction::TRANSACTION_STATUS_APPROVED);

        return $query;
    }
    
    /*
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:Withdrawal:edit.html.twig';
    }
}
