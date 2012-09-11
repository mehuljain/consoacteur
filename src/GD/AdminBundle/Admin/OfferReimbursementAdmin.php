<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use GD\AdminBundle\Entity\Offer;
use Doctrine\ORM\EntityRepository;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for Full Reimbursement Offers.
 *  Only records with offer type as OFFER_TYPE_FULL_REIMBURSEMENT are fetched.
 */
class OfferReimbursementAdmin extends SonataAdmin
{
    protected $baseRouteName = 'offer_fr';
    protected $baseRoutePattern = 'offers/fullreimbursement';
    
    protected $offerId = null;

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
        $offerId = $this->getOfferId();
        if(is_null($offerId)) {
            $formMapper
                ->add('merchant', null, array('attr' => array('class' => 'chzn-select')));
        } else {
            $formMapper
                ->add('merchant', null, array('read_only' => true, 'attr' => array('class' => 'chzn-select')));
        }
        $formMapper
            ->add('type', 'hidden', array('attr' => array('value' => Offer::OFFER_TYPE_FULL_REIMBURSEMENT)))
            ->add('name', null, array('attr' => array('class' => 'translatable')))
            ->add('slug', null, array('required' => false, 'help' => $this->translator->trans('slug.emptynotification.message')))
            ->add('programId')
            ->add('redirectionUri', null, array('help' => $this->translator->trans('offer.redirectionurlnotification.message')))
            ->add('displayUri', null, array('required' => false, 'help' => $this->translator->trans('offer.redirectionurlnotification.message')))
            ->add('description', null, array('attr' => array('class' => 'tinymce translatable'), 'required' => false, 'help' => $this->translator->trans('mandatoryfield.notification.message')))
            ->add('userGainValue', null, array('attr' => array('class' => 'translatable'), 'help' => $this->translator->trans('offer.usergaincashbacknotification.message')))
            ->add('startDate', null, array(
                'format' => 'yyyy-MMMM-dd',
                'years' => range(date('Y'),date('Y')+5),
            ))
            ->add('endDate', null, array(
                'format' => 'yyyy-MMMM-dd',
                'years' => range(date('Y'),date('Y')+5),
            ))
            ->add('isCurrent')
            ->add('fullReimbursementMaxWinAmount', null, array('help' => $this->translator->trans('fullreimburse.maxwinamountnotification.message')))
            ->add('fullReimburseMinParticipant')
            ->add('fullReimburseMinTransAmount', null, array('help' => $this->translator->trans('fullreimburse.mintransactionnotification.message')))
            ->add('fullReimburseCashbackPercent', 'percent', array('type' => 'integer', 'precision' => '2', 'help' =>  $this->translator->trans('cashbackpercentage.notification.message') ))
            ->add('fullReimbursementTermsCondition', null, array('attr' => array('class' => 'translatable')))
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
        $listMapper
            ->addIdentifier('name')
            ->add('merchant')
            ->add('programId')
            ->add('startDate')
            ->add('endDate')
            ->add('isCurrent')
            ->add('isArchived')
            ->add('merchant.affiliatePartner');
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
            ->assertMaxLength(array('limit' => 255))
            ->end()
            ->with('fullReimbursementMaxWinAmount')
            ->assertNotBlank()
            ->end()
            ->with('fullReimburseMinParticipant')
            ->assertNotBlank()
            ->end()
            ->with('fullReimburseMinTransAmount')
            ->assertNotBlank()
            ->end()
            ->with('fullReimburseCashbackPercent')
            ->assertNotBlank()
            ->end();

        $today = new \DateTime('today');

        if ($this->getSubject()->getEndDate() < $today) {
            $errorElement
                ->with('endDate')
                    ->addViolation('End date cannot be less than today')
                ->end();
        }

        if ($this->getSubject()->getStartDate() < $today) {
            $this->getSubject()->setStartDate($today);
        }
    }
    
    /*
     *  Custom query to filter the records 
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'o');
        $query->where('o.type = :type')
            ->setParameter('type', Offer::OFFER_TYPE_FULL_REIMBURSEMENT);
        $query->andWhere('o.isArchived = false');

        return $query;
    }

    /*
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:Offer:edit.html.twig';
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
