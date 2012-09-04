<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GD\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Security\Acl\Permission\MaskBuilder;

use FOS\UserBundle\Model\UserManagerInterface;

/*
 *  This class represents the CRUD definition for User entity
 *  Backend users are created exclusively using fixtures or the command line in production
 *  These users can login to the admin/dashboard and perform a number of features based on their roles
 *  The sonata.user.admin.user service, defined in services.yml can be used to manipulate this class
 */
class BackendUserAdmin extends Admin
{   
    
    protected $formOptions = array(
        'validation_groups' => 'Profile'
    );

    /*
     *  The fields displayed in the list table
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('enabled')
            ->add('locked')
            ->add('createdAt')
        ;

        /*
        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
            ;
        }
        */
    }

    /*
     * The fields available for filtering the list
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('username')
            ->add('locked')
            ->add('email')
            ->add('id')
        ;
    }

    /* 
     *  The fields used to edit the entity
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('username')
                ->add('email')
                ->add('plainPassword', 'text', array('required' => false))
            ->end()
            ->with('Groups')
                ->add('groups', 'sonata_type_model', array('required' => false))
            ->end()
            ->with('Management')
                ->add('roles', 'sonata_security_roles', array( 'multiple' => true, 'required' => false))
                ->add('enabled', null, array('required' => false))
            ->end()
        ;
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
     *  Custom query to filter the records 
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass(), 'u');
        $query->where('u.isAdminUser = true');

        return $query;
    }
}
