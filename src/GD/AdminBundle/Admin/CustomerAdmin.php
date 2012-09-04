<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use FOS\UserBundle\Model\UserManagerInterface;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for Customers (User entity)
 *  Customers are regular users that register from the frontend of the website. 
 *  Only records that have the isAdminUser flag set as false and those that are not blacklisted or archived are displayed.
 */
class CustomerAdmin extends SonataAdmin
{
    protected $baseRouteName = 'customer';
    protected $baseRoutePattern = 'customer';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('resetPassword', $this->getRouterIdParameter() . '/resetPassword');
        $collection->add('archive', $this->getRouterIdParameter() . '/archive');
        $collection->add('blacklist', $this->getRouterIdParameter() . '/blacklist');
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
            ->with('Personal Details')
                ->add('salutation', null, array('attr' => array('readonly' => true)))
                ->add('firstName', null, array('attr' => array('readonly' => true)))
                ->add('lastName', null, array('attr' => array('readonly' => true)))
                ->add('dateOfBirth', 'date', array('read_only' => true))
                ->add('profession', null, array('attr' => array('readonly' => true)))
                ->add('genderAsString', 'text', array('attr' => array('readonly' => true)))
            ->with('Address Details')
                ->add('apartmentNumber', null, array('attr' => array('readonly' => true)))
                ->add('addressLocation', null, array('attr' => array('readonly' => true)))
                ->add('locationName', null, array('attr' => array('readonly' => true)))
                ->add('complementaryAddressDetails', null, array('attr' => array('readonly' => true)))
                ->add('zipcode', null, array('attr' => array('readonly' => true)))
                ->add('city', null, array('attr' => array('readonly' => true)))
                ->add('country', null, array('attr' => array('readonly' => true)))
                ->add('phoneHome', null, array('attr' => array('readonly' => true)))
                ->add('phoneMobile', null, array('attr' => array('readonly' => true)))
                ->add('phoneOffice', null, array('attr' => array('readonly' => true)))
                ->add('email', null, array('attr' => array('readonly' => true)))
                ->add('shareContact', null, array('read_only' => true))
            ->with('Adevertising Details')
                ->add('newsletterSubscription', null, array('read_only' => true))
            ->with('Account Status')
                ->add('enabled', null, array('read_only' => true))
                ->add('accountClosureReason', null, array('attr' => array('readonly' => true)))
            ->with('Blacklist Status')
                ->add('blacklistReason', null, array('attr' => array('class' => 'blacklist sonata-medium')))
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
            ->add('username')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
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
            ->add('id')
            ->addIdentifier('username')
            ->add('fullName', null, array('sortable' => true))
            ->add('genderAsString', null, array('sortable' => true, 'label' => 'Gender'))
            ->add('age', null, array('sortable' => true))
            ->add('locationName')
        ;
    }

    /*
     *  Custom query to filter the records 
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'o');
        $query->where('o.isAdminUser = false AND o.isArchived = false AND o.isBlacklisted = false');

        return $query;
    }

    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getUserManager()
    {
        return $this->userManager;
    }

    /*
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:Customer:edit.html.twig';
    }

    public function getPartialUrlForTransaction()
    {
        $host = $this->getRequest()->getHost();
        $path = $this->generateUrl('list');
        $routePattern = $this->getBaseRoutePattern();
        $partialRoute = strstr($path, $routePattern, true);
        return 'http://'.$host.$partialRoute;
    }
}
