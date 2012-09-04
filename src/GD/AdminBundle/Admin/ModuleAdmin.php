<?php

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use GD\AdminBundle\Entity\Module;

use GD\AdminBundle\Admin\Sonata\SonataAdmin;

/*
 *  This class represents the CRUD definition for the Module entity.
 *  Modules are placeholders for HTML code or a list of Categories.
 *  Upto 4 modules can be displayed in the application frontend under the Menu Section
 */
class ModuleAdmin extends SonataAdmin
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
            ->add('type', 'choice', array(
                'choices'   => array(Module::MODULE_TYPE_CATEGORIES => 'Categories', Module::MODULE_TYPE_HTML => 'Html'),
                'expanded' => true,
                'multiple'  => false,
            ))
            ->add('name')
            ->add('categories', 'sonata_type_model', array('expanded' => false, 'multiple' => true, 'attr' => array('class' => 'chzn-select')))
            ->add('html', null, array('attr' => array('required' => false)))
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
            ->add('type')
            ->add('name');
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
            ->add('type')
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
     *  Redirects to custom edit template instead of the default CRUD base_edit template
     */
    public function getEditTemplate()
    {
        return 'GDAdminBundle:Module:edit.html.twig';
    }
}
