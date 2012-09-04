<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use GD\AdminBundle\Entity\MerchantList;
use Doctrine\ORM\EntityRepository;
use GD\AdminBundle\Entity\Offer;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for MerchantList entity.
 *
 *  Merchant List is an abstraction to group a subset of merchants. 
 *  There are two kinds of lists, 'Normal Lists' and 'Carousel Lists'
 *  Only those records are fetched that have type as LIST_TYPE_CAROUSEL. 
 *
 *  This class corresponds to the "Cashback" and "Codepromo" merchant lists that are displayed on the 
 *  homepage of the website frontend.
 *  
 *  Note: The admin user can drag the merchants to change the order in which they are displayed in the frontend
 */
class CarouselListAdmin extends SonataAdmin
{
    protected $baseRouteName = 'merchant_carousel_list';
    protected $baseRoutePattern = 'merchantlists/carousel';

    protected $offerType;

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     *
     *  The fields used to edit the entity
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $offerType = $this->getOfferType();

        $formMapper
            ->add('name', null, array('attr' => array('readonly' => true)))
            ->add('merchants', 'entity', array('help' => $this->translator->trans('carousel.notification.message'),
                'class' => 'GDAdminBundle:Merchant',
                'query_builder' => function(EntityRepository $er) use($offerType) {
                    $query = $er->createQueryBuilder('m')
                        ->join('m.offers', 'o')
                        ->where('m.isActive = true AND m.isArchived = false AND o.type = :offerType AND o.isCurrent = true AND o.isArchived = false AND o.startDate <= CURRENT_DATE() AND o.endDate >= CURRENT_DATE()')
                        ->setParameter('offerType', $offerType)
                    ;
                    if ($offerType == Offer::OFFER_TYPE_CASHBACK) {
                        $query->orderBy('m.cashbackSortOrder', 'ASC');
                    } elseif ($offerType == Offer::OFFER_TYPE_CODE_PROMO) {
                        $query->orderBy('m.codepromoSortOrder', 'ASC');
                    }

                    return $query;
                },
                'multiple' => true,
                'expanded' => true,
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
     *  ($context = 'list') is provided in the arguments to make it consistent with AdminInterface's createQuery method
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'o');
        $query->where('o.type = :carouselType');
        $query->setParameter('carouselType', MerchantList::LIST_TYPE_CAROUSEL);

        return $query;
    }

    /*
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:CarouselList:edit.html.twig';
    }

    public function setOfferType($offerType)
    {
        $this->offerType = $offerType;
    }

    public function getOfferType()
    {
        return $this->offerType;
    }
}
