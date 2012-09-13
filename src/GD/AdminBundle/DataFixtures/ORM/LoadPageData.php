<?php

namespace GD\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use GD\AdminBundle\Entity\Page;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPageData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faq = new Page();
        $faq->setName("FAQ");
        $faq->setSlug("faq");
        $faq->setCreatedAt(new \DateTime());
        $faq->setUpdatedAt(new \DateTime());
        $faq->setMetaTags("faq");
        $faq->setMetaDescription("Great Deals Question and Answers");
        $faq->setContent('
            <h1 class="title">FAQ</h1>');
        $manager->persist($faq);

        $assistance = new Page();
        $assistance->setName("Assistance");
        $assistance->setSlug("assistance");
        $assistance->setCreatedAt(new \DateTime());
        $assistance->setUpdatedAt(new \DateTime());
        $assistance->setMetaTags("assistance");
        $assistance->setMetaDescription("Conso-Acteur");
        $assistance->setContent("<p>Need Assistance?</p>");
        $manager->persist($assistance);
        
        $tnc = new Page();
        $tnc->setName("CGU");
        $tnc->setSlug("terms-and-conditions");
        $tnc->setCreatedAt(new \DateTime());
        $tnc->setUpdatedAt(new \DateTime());
        $tnc->setMetaTags("cgu");
        $tnc->setMetaDescription("CGU");
        $tnc->setContent("<p>Terms and Condition goes in here!</p>");
        $manager->persist($tnc);
        
        //Footer Pages
        $copyright = new Page();
        $copyright->setName("Copyright Act");
        $copyright->setSlug("copyright-act");
        $copyright->setCreatedAt(new \DateTime());
        $copyright->setUpdatedAt(new \DateTime());
        $copyright->setMetaTags("copyright");
        $copyright->setMetaDescription("ConsoActeur");
        $copyright->setContent("<p>Copyright content goes in here!</p>");
        $manager->persist($copyright);
        
        $sitemap = new Page();
        $sitemap->setName("Sitemap");
        $sitemap->setSlug("sitemap");
        $sitemap->setCreatedAt(new \DateTime());
        $sitemap->setUpdatedAt(new \DateTime());
        $sitemap->setMetaTags("sitemap");
        $sitemap->setMetaDescription("ConsoActeur sitemap");
        $sitemap->setContent("<h1>Sitemap</h1><p>En attente!</p>");
        $manager->persist($sitemap);
        
        $rssFeed = new Page();
        $rssFeed->setName("RSS feed");
        $rssFeed->setSlug("rss-feed");
        $rssFeed->setCreatedAt(new \DateTime());
        $rssFeed->setUpdatedAt(new \DateTime());
        $rssFeed->setMetaTags("rss feed");
        $rssFeed->setMetaDescription("Great Deals Rss Feed");
        $rssFeed->setContent("<h1>RSS Feed</h1><p>RSS Feed links goes in here!</p>");
        $manager->persist($rssFeed);
        
        //FAQ Pages
        $userEarningsFAQ = new Page();
        $userEarningsFAQ->setName("User Earnings FAQ");
        $userEarningsFAQ->setSlug("user-earnings-faq");
        $userEarningsFAQ->setCreatedAt(new \DateTime());
        $userEarningsFAQ->setUpdatedAt(new \DateTime());
        $userEarningsFAQ->setMetaTags("User earnings faq");
        $userEarningsFAQ->setMetaDescription("Great Deals User Earnings FAQ");
        $userEarningsFAQ->setContent("<h1>User Earnings FAQ</h1><p>User Earnings FAQ goes in here!</p>");
        $manager->persist($userEarningsFAQ);
        
        $manager->flush();
    }

    public function getOrder()
    {
        return 7;
    }
}
