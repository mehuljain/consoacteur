<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use GD\AdminBundle\Entity\Offer;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Route\RouteCollection;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for archived offers.
 *  Only those records are fetched that have isArchived flag is set to true.
 */
class OfferArchivedAdmin extends SonataAdmin
{
    protected $baseRouteName = 'offer_archived';
    protected $baseRoutePattern = 'offers/archived';

    protected $offerId;
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('duplicate', $this->getRouterIdParameter() . '/dupliate');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     *
     *  The fields used to edit the entity
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $offerId = $this->getOfferId();
        $formMapper
            ->add('merchant', null, array('attr' => array('class' => 'chzn-select'), 'read_only' => true))
            ->add('type', null, array('read_only' => true))
            ->add('name', null, array('read_only' => true))
            ->add('slug', null, array('required' => false, 'help' => 'Please leave this field blank if creating a new object'))
            ->add('programId', null, array('read_only' => true))
            ->add('redirectionUri', null, array('read_only' => true))
            ->add('displayUri', null, array('read_only' => true))
            ->add('userGainValue', null, array('read_only' => true))
            ->add('userGainPercentage', null, array('help' => 'Only numerals, no % sign required. Example: 7.50', 'read_only' => true))
            ->add('description', null, array('attr' => array('class' => 'tinymce'), 'required' => false, 'read_only' => true))
            ->add('startDate', null, array(
                'empty_value' => array('year' => date('Y'), 'month' => date('F'), 'day' => date('d')),
                'format' => 'yyyy-MMMM-dd',
                'years' => range(date('Y')-5,date('Y')+5),
                'read_only' => true
            ))
            ->add('endDate', null, array(
                'format' => 'yyyy-MMMM-dd',
                'years' => range(date('Y')-5,date('Y')+5),
                'read_only' => true))
            ->add('isCurrent', null, array('read_only' => true))
            ->add('cashbackValuePercentage', null, array('read_only' => true))
            ->add('cashbackValueAmount', null, array('read_only' => true))
            ->add('fullReimbursementMaxWinAmount', null, array('read_only' => true))
            ->add('fullReimburseMinParticipant', null, array('read_only' => true))
            ->add('fullReimburseMinTransAmount', null, array('read_only' => true))
            ->add('fullReimburseCashbackPercent', null, array('read_only' => true))
            ->add('fullReimbursementTermsCondition', null, array('read_only' => true))
            ->add('affiliatePartner', 'entity', array(
                'class' => 'GDAdminBundle:Merchant',
                'property' => 'affiliatePartner',
                'query_builder' => function(EntityRepository $er) use($offerId) {
                    $query = $er->createQueryBuilder('m')
                        ->select('m')
                        ->join('m.offers', 'o')
                        ->where('o.id = :offerId')
                        ->setParameter('offerId', $offerId)
                    ;
                    return $query;
                },
                'multiple' => false,
                'expanded' => false,
                'label' => 'Affiliate Partner',
                'read_only' => true,
            ))
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
            ->add('merchant')
            ->add('programId')
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
        $offerId = $this->getOfferId();
        
        $listMapper
            ->addIdentifier('name')
            ->add('merchant')
            ->add('programId')
            ->add('startDate')
            ->add('endDate')
            ->add('isCurrent')
            ->add('isArchived')
            ->add('affiliatePartner', 'sonata_type_model', array('required' => false))
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
        $query->where('o.isArchived = true');

        return $query;
    }

    /*
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:CRUD:archive_duplicate.html.twig';
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
    
    public function setOfferId($id)
    {
        $this->offerId = $id;
    }
    
    public function getOfferId()
    {
        return $this->offerId;
    }
}
