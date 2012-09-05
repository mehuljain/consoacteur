<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Group;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAdminGroupsData extends AbstractFixture implements OrderedFixtureInterface {
  
   protected $adminRoles = array(
        'ROLE_SONATA_USER_ADMIN_USER_EDIT',
        'ROLE_SONATA_USER_ADMIN_USER_LIST',
        'ROLE_SONATA_USER_ADMIN_USER_CREATE',
        'ROLE_SONATA_USER_ADMIN_USER_VIEW',
        'ROLE_SONATA_USER_ADMIN_USER_DELETE',
        'ROLE_SONATA_USER_ADMIN_USER_OPERATOR',
        'ROLE_SONATA_USER_ADMIN_USER_MASTER',
        'ROLE_SONATA_USER_ADMIN_GROUP_EDIT',
        'ROLE_SONATA_USER_ADMIN_GROUP_LIST',
        'ROLE_SONATA_USER_ADMIN_GROUP_CREATE',
        'ROLE_SONATA_USER_ADMIN_GROUP_VIEW',
        'ROLE_SONATA_USER_ADMIN_GROUP_DELETE',
        'ROLE_SONATA_USER_ADMIN_GROUP_OPERATOR',
        'ROLE_SONATA_USER_ADMIN_GROUP_MASTER',
        'ROLE_ADMIN'
    );
  /**
   * Light roles will have LIST AND VIEW actions
   * @var type 
   */
  protected $rolesLight = array(
      'ROLE_GD_ADMIN__NAME__LIST',
      'ROLE_GD_ADMIN__NAME__VIEW',
  );
  
  /**
   *  Full access is provided only for specific group users. For details about the roles please 
   *  read Symfony2 ACL roles
   * @var type 
   */  
  protected $rolesFull = array(
      'ROLE_GD_ADMIN__NAME__LIST',
      'ROLE_GD_ADMIN__NAME__VIEW',
      'ROLE_GD_ADMIN__NAME__EDIT',    
      'ROLE_GD_ADMIN__NAME__CREATE',      
      'ROLE_GD_ADMIN__NAME__DELETE',
  );
  
  /*
   * Assign access roles for objects to user groups
   * Remove ROLE_**_CREATE to disable the "Add New" button from backend
   */
  public function load(ObjectManager $manager) {
   
    /**
     * All content management pages must be added here. The list of pages can 
     * be obtained from services.yml under the Admin Bundle
     */
    $cmsGroup = array(
        'PAGE',
        'CATEGORY',
        'TAG',
        'MERCHANT',
        'MERCHANT_LIST',
        'OFFER_CASHBACK',
        'OFFER_REIMBURSEMENT',
        'OFFER_CODEPROMO',
        'OFFER_SUBSCRIPTION',
        'MODULE',
        'CAROUSEL_LIST',
        'FLASH_PARAMETER',
        'NEWSLETTER');

    /**
     * All customer(registered user) related pages must be added here
     */
    $customerGroup = array(
        'CUSTOMER_DETAILS',
        'CUSTOMERS_BLACKLISTED',
        'CUSTOMERS_ARCHIVED');
    
    /**
     * Feedback module group 
     */
    $feedbackGroup = array(
        'FEEDBACK'
    );

    /**
     * All transaction related pages in the backend 
     */
    $transactionGroup = array(
        'TRANSACTION',
        'WITHDRAWAL_PENDING',
        'WITHDRAWAL_APPROVED',
        'WITHDRAWAL_PAID',
        'WITHDRAWAL_ON_HOLD');

    $customerRoles = $customerLightRoles =  
    $transactionRoles = $transactionLightRoles = 
    $cmsRoles = $cmsLightRoles = 
    $feedbackRoles = $feedbackLightRoles = array();

    foreach ($cmsGroup as $cms) {
      //Provide Full Access to cmsRoles
      foreach ($this->rolesFull as $roleFull) {
        $cmsRoles[] = str_replace('__NAME__', '_' . $cms . '_', $roleFull);
      }
      //Provide Light Access to cmsLightRoles
      foreach ($this->rolesLight as $roleLight){
        $cmsLightRoles[] = str_replace('__NAME__', '_' . $cms. '_', $roleLight);
      }
    }
        
    foreach ($customerGroup as $customer) {
      //Provide Full Access to customerRoles
      foreach ($this->rolesFull as $role) {
        $customerRoles[] = str_replace('__NAME__', '_' . $customer . '_', $role);
      }
      //Provide Light Access to cmsLightRoles
      foreach ($this->rolesLight as $roleLight){
        $customerLightRoles[] = str_replace('__NAME__', '_' . $customer. '_', $roleLight);
      }
    }

    foreach ($transactionGroup as $transaction) {
      //Provide Full access to transaction roles
      foreach ($this->rolesFull as $role) {
        $transactionRoles[] = str_replace('__NAME__', '_' . $transaction . '_', $role);
      }
      //Provide Light Access to transaction roles
      foreach ($this->rolesLight as $roleLight){
        $transactionLightRoles[] = str_replace('__NAME__', '_' . $transaction. '_', $roleLight);
      }
    }
    
    foreach ($feedbackGroup as $feedback) {
      //Provide Full access to feedback roles
      foreach ($this->rolesFull as $role) {
        $feedbackRoles[] = str_replace('__NAME__', '_' . $feedback . '_', $role);
      }
      //Provide Light Access to feedback roles
      foreach ($this->rolesLight as $roleLight){
        $feedbackLightRoles[] = str_replace('__NAME__', '_' . $feedback. '_', $roleLight);
      }
    }
    
    //Provide ROLE_ADMIN access so that the user can access backend pages
    $customerRoles[] = $customerLightRoles[] =  
    $transactionRoles[] = $transactionLightRoles[] = 
    $cmsRoles[] = $cmsLightRoles[] = 
    $feedbackRoles[] = $feedbackLightRoles[] = 'ROLE_ADMIN';
    
    //Persist all roles to the database. The first argument is the name and the second the array of roles.
    $customer = new Group('customer', $customerRoles);
    $manager->persist($customer);
    
    $customerLight = new Group('customerLight', $customerLightRoles);
    $manager->persist($customerLight);

    $transaction = new Group('transaction', $transactionRoles);
    $manager->persist($transaction);
    
    $transactionLight = new Group('transactionLight', $transactionLightRoles);
    $manager->persist($transactionLight);

    $cms = new Group('cms', $cmsRoles);
    $manager->persist($cms);
    
    $cmsLight = new Group('cmsLight', $cmsLightRoles);
    $manager->persist($cmsLight);
    
    $feedback = new Group('feedback', $feedbackRoles);
    $manager->persist($feedback);
    
    $feedbackLight = new Group('feedbackLight', $feedbackLightRoles);
    $manager->persist($feedbackLight);
     
    $admin = new Group('admin', $this->adminRoles);
    $manager->persist($admin);
    
      $manager->flush();
      $this->addReference('admin-group', $admin);
    }

  public function getOrder() {
    return 9;
  }

}
