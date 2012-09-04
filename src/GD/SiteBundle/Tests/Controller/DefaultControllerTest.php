<?php

namespace GD\SiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /*public function testBasicSiteLayout()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('div.popup')->count());
        $this->assertEquals(1, $crawler->filter('div.newsl_popup')->count());
        $this->assertEquals(1, $crawler->filter('div.forgot_pass_popup')->count());
        $this->assertEquals(1, $crawler->filter('div#lightbox')->count());
        $this->assertEquals(1, $crawler->filter('div#main')->count());
            $this->assertEquals(1, $crawler->filter('div#header')->count());
            $this->assertEquals(1, $crawler->filter('div#menu')->count());
            $this->assertEquals(1, $crawler->filter('div#body')->count());
            $this->assertEquals(1, $crawler->filter('div#footer')->count());

    }


    public function testSiteHeader()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(1, $crawler->filter('#header div#logo')->count());
            $this->assertEquals(1, $crawler->filter('#header a.logo')->count());
            $this->assertEquals(1, $crawler->filter('#header a.tagline')->count());

        $this->assertEquals(1, $crawler->filter('#header div.middle')->count());
        $this->assertEquals(1, $crawler->filter('#header div.navigation')->count());
            $this->assertEquals(4, $crawler->filter('#header div.navigation a')->count());
            $this->assertEquals(1, $crawler->filter('#header div.search')->count());

        $this->assertEquals(1, $crawler->filter('#header div.identification')->count());
            $this->assertEquals(1, $crawler->filter('#header div.lang')->count());
            $this->assertEquals(1, $crawler->filter('#header div.form')->count());
            $this->assertEquals(1, $crawler->filter('#header div.social')->count());

    }

    public function testSiteNavigation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(1, $crawler->filter('#menu div.left')->count());
            $this->assertEquals(1, $crawler->filter('#menu div.middle')->count());
            $this->assertEquals(1, $crawler->filter('#menu ul#topnav')->count());

        $this->assertEquals(1, $crawler->filter('#menu div.other_links')->count());
        $this->assertEquals(1, $crawler->filter('#menu div.right')->count());
            $this->assertEquals(7, $crawler->filter('#menu ul#topnav li')->count());
            $this->assertEquals(1, $crawler->filter('#menu div.other_links ul li')->count());
    }

    public function testSiteBody()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(1, $crawler->filter('#body div.banner')->count());
        $this->assertEquals(1, $crawler->filter('#body div.content')->count());
            $this->assertEquals(1, $crawler->filter('#body div.content div.cashback ')->count());
            $this->assertEquals(1, $crawler->filter('#body div.content div.codes_promo ')->count());
            $this->assertEquals(1, $crawler->filter('#body div.content div.inscription ')->count());

    }

    public function testSiteFooter()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(1, $crawler->filter('#footer div.footer')->count());
        $this->assertEquals(1, $crawler->filter('#footer div.footer ul')->count());
            $this->assertEquals(4, $crawler->filter('#footer div.footer ul li')->count());

    }

    public function testSiteLogoTagClick(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $logoLink = $crawler->filter('#header a:contains("Logo")')->eq(0)->link();
        $uri = $logoLink->getUri();
        $client->click($logoLink);
        //$this->assertEquals(200, $client->getResponse()->getStatusCode());

        $tagLink = $crawler->filter('#header a:contains("La consommation intelligente")')->eq(0)->link();
        $uri = $tagLink->geturi();
        $client->click($tagLink);
        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSiteTopNavLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Parrainage')->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('FAQ')->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Newsletter')->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Assistance')->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteSearch()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $buttonCrawlerNode = $crawler->selectButton('ok');
        $form = $buttonCrawlerNode->form();
        $client->submit($form, array('search' => 'Kiabi.com'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteLangChange()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Ch-Fr')->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Ch-De')->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $buttonCrawlerNode = $crawler->selectButton('login');
        $form = $buttonCrawlerNode->form();
        $client->submit($form,array('username' => 'asfasf', 'password' => 'dhasdkjh'));
        //$crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSiteRegisterLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Inscription')->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteForgotPasswordLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Forgot Password ?')->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteFacebookLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Facebook')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteTwitterLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Twitter')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteMenuLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('How does it work?')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Le concept')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Our Categories')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Cashback')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('code promo')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Full Reimbursement')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Subscription Gain')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteCashbackBlockLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('See Our Merchants')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSiteCodePromoBlockLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('See Our Merchants')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSiteHomepageRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $link = $crawler->selectButton('See Our Merchants');
        $form = $link->form();
        $client->submit($form,array('radio' => 'M', 'username' => 'dhasdkjh', 'password' => 'scsc', 'email' => 'asdsd@email.com', 'agreeCheckBox' => 'checked'));
        //$crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSiteFooterLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Plan du site')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Imprint')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Contactez-nous')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Other')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Pays')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Website Links')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('S\'inscrire')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Avantages')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Charte')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Recrutement')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('CNIL')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Sommaire')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSiteFooterNewsletter()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $buttonCrawlerNode = $crawler->selectButton('validate');
        $form = $buttonCrawlerNode->form();
        $client->submit($form,array('news_email' => 'asdsd@email.com'));
        //$crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }*/
    
    /* FR-17.1.1-011 */
    /*public function testHowItWorks()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');
        $link = $crawler->selectLink('How does it work?')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertTrue($crawler->filter('html:contains("HOW DOES IT WORK?")')->count() == 1);
        
    }*/

    /* FR-17.1.7-004 */
    /*public function testCBOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Cashback')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers/1',$link->getUri());       
        
        $client2 = static::createClient();
        $crawler2 = $client2->request('GET', $link->getUri());        
        $this->assertTrue($crawler2->filter('html:contains("Recherche par ordre alphabétique")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Marchands")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Cashback")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Codes Promo")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Subscription gain")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Full Reimburse")')->count() == 1);
        
    }*/

    /* FR-17.1.7-005 */
    /*public function testCPOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Code Promo')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers/3',$link->getUri());
        
        $client2 = static::createClient();
        $crawler2 = $client2->request('GET', $link->getUri()); 
        $this->assertTrue($crawler2->filter('html:contains("Marchands")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Cashback")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Codes Promo")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Subscription gain")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Full Reimburse")')->count() == 1);

    }*/

    /* FR-17.1.7-005 */
    /*public function testFROffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Full Reimbursement')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers/2',$link->getUri());
        
        $client2 = static::createClient();
        $crawler2 = $client2->request('GET', $link->getUri());
        $this->assertTrue($crawler2->filter('html:contains("Marchands")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Cashback")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Codes Promo")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Subscription gain")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Full Reimburse")')->count() == 1);

    }*/

    /* FR-17.1.7-005 */
    /*public function testSGOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $link = $crawler->selectLink('Subscription Gain')->link();
        $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers/4',$link->getUri());
        
        $client2 = static::createClient();
        $crawler2 = $client2->request('GET', $link->getUri());
        $this->assertTrue($crawler2->filter('html:contains("Marchands")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Cashback")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Codes Promo")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Subscription gain")')->count() == 1);
        $this->assertTrue($crawler2->filter('html:contains("Full Reimburse")')->count() == 1);

    }*/

    /* FR-17.1.7-010 */
    /*public function testSearchIndexOnCBOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/1');
        $searchIndex = $crawler->filter('.search_by_alpha ul li a');
        $searchLink = $searchIndex->selectLink('a')->link();
        $client->click($searchLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers?search_key=a',$searchLink->getUri());


    }
    
    public function testSearchIndexOnCPOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/3');
        $searchIndex = $crawler->filter('.search_by_alpha ul li a');
        $searchLink = $searchIndex->selectLink('a')->link();
        $client->click($searchLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers?search_key=a',$searchLink->getUri());


    }
    
    public function testSearchIndexOnFROffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/2');
        $searchIndex = $crawler->filter('.search_by_alpha ul li a');
        $searchLink = $searchIndex->selectLink('a')->link();
        $client->click($searchLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers?search_key=a',$searchLink->getUri());

        $searchLink2 = $searchIndex->selectLink('b')->link();
        $client->click($searchLink2);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers?search_key=b',$searchLink2->getUri());

    }
    
    public function testSearchIndexOnSGOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/4');
        $searchIndex = $crawler->filter('.search_by_alpha ul li a');
        $searchLink = $searchIndex->selectLink('a')->link();
        $client->click($searchLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers?search_key=a',$searchLink->getUri());
        
        $searchLink2 = $searchIndex->selectLink('b')->link();
        $client->click($searchLink2);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers?search_key=b',$searchLink2->getUri());


    }*/
    
    /*public function testPaginationOnCBOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/1');
        $pageIndex = $crawler->filter('.paging li a');
        $pageLink = $pageIndex->selectLink('2')->link();
        $client->click($pageLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers/1?page=2',$pageLink->getUri());
        
        $pageLinkNext = $pageIndex->selectLink('Suivante')->link();
        $client->click($pageLinkNext);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }*/
    
    /*public function testPaginationOnCPOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/3');
        $pageIndex = $crawler->filter('.paging li a');
        $pageLink = $pageIndex->selectLink('2')->link();
        $client->click($pageLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers/1?page=2',$pageLink->getUri());
        
        $pageLinkNext = $pageIndex->selectLink('Suivante')->link();
        $client->click($pageLinkNext);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }*/
    
    /*public function testPaginationOnFBOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/2');
        $pageIndex = $crawler->filter('.paging li a');
        $pageLink = $pageIndex->selectLink('2')->link();
        $client->click($pageLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers/1?page=2',$pageLink->getUri());
        
        $pageLinkNext = $pageIndex->selectLink('Suivante')->link();
        $client->click($pageLinkNext);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }*/
    
    /*public function testPaginationOnSGOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/4');
        $pageIndex = $crawler->filter('.paging li a');
        $pageLink = $pageIndex->selectLink('2')->link();
        $client->click($pageLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/en/offers/1?page=2',$pageLink->getUri());
        
        $pageLinkNext = $pageIndex->selectLink('Suivante')->link();
        $client->click($pageLinkNext);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }*/
    
    //Not working!
   /*public function testMerchantClickOnCBOffersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/offers/1');
        $merchantLink = $crawler->filter('.offer_cont ul li');
        $client->click($merchantLink);
        
        
    } */ 
    
    
    
    
    


}
