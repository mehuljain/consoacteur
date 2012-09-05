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
            <h1 class="title">FAQ</h1>
                <div id="faq-topics">
                <ul class="floatl">
                <li><a href="#general">General</a></li>
                <li><a href="#cashback">Cashback</a></li>
                <li><a href="#codepromo">Code Promo</a></li>
                <li><a href="#sgain">Subscription Gain</a></li>
                </ul>
                <ul>
                <li><a href="#full-reimbru">Full Reimbruisement</a></li>
                <li><a href="#referralp">Referral Programme</a></li>
                <li><a href="#wap">Withdrawals and Payments</a></li>
                </ul>
                </div>
                <div class="faq-block"><a name="general"></a>
                <h4>General<a class="top" href="#top">Top</a></h4>
                <ul>
                <li>
                <p class="question"><span>Q.</span>Is joining Great-Deals free?</p>
                <p class="answer"><span>A.</span>Yes, and you also get 3 Euros bonus on joining.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>What is automatic invoicing?</p>
                <p class="answer"><span>A.</span>This service is only available to individuals. It enables our system to automatically create your invoice for the past month without you having to do anything: you won\'t receive any invoice requests and won\'t have to send us an invoice. A copy of the invoice will be sent to you by e-mail (you should keep it as proof) and another copy will be kept by NetAffiliation. You will receive payment at the end of the month via your chosen method of payment.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much does it cost?</p>
                <p class="answer"><span>A.</span>Webmaster registration is ENTIRELY FREE OF CHARGE! Affiliation is for you to make money, not spend it!</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much will I earn?</p>
                <p class="answer"><span>A.</span>That depends on each campaign.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How do I collect my earnings?</p>
                <p class="answer"><span>A.</span>NetAffiliation will collect your earnings from the advertisers and pay them to you every month once they reach a certain threshold. This threshold depends on the currency of payment.</p>
                </li>
                </ul>
                </div>
                <div class="faq-block"><a name="cashback"></a>
                <h4>Cashback<a class="top" href="#top">Top</a></h4>
                <ul>
                <li>
                <p class="question"><span>Q.</span>Is joining Great-Deals free?</p>
                <p class="answer"><span>A.</span>Yes, and you also get 3 Euros bonus on joining.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>What is automatic invoicing?</p>
                <p class="answer"><span>A.</span>This service is only available to individuals. It enables our system to automatically create your invoice for the past month without you having to do anything: you won\'t receive any invoice requests and won\'t have to send us an invoice. A copy of the invoice will be sent to you by e-mail (you should keep it as proof) and another copy will be kept by NetAffiliation. You will receive payment at the end of the month via your chosen method of payment.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much does it cost?</p>
                <p class="answer"><span>A.</span>Webmaster registration is ENTIRELY FREE OF CHARGE! Affiliation is for you to make money, not spend it!</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much will I earn?</p>
                <p class="answer"><span>A.</span>That depends on each campaign.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How do I collect my earnings?</p>
                <p class="answer"><span>A.</span>NetAffiliation will collect your earnings from the advertisers and pay them to you every month once they reach a certain threshold. This threshold depends on the currency of payment.</p>
                </li>
                </ul>
                </div>
                <div class="faq-block"><a name="codepromo"></a>
                <h4>Code Promo<a class="top" href="#top">Top</a></h4>
                <ul>
                <li>
                <p class="question"><span>Q.</span>Is joining Great-Deals free?</p>
                <p class="answer"><span>A.</span>Yes, and you also get 3 Euros bonus on joining.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>What is automatic invoicing?</p>
                <p class="answer"><span>A.</span>This service is only available to individuals. It enables our system to automatically create your invoice for the past month without you having to do anything: you won\'t receive any invoice requests and won\'t have to send us an invoice. A copy of the invoice will be sent to you by e-mail (you should keep it as proof) and another copy will be kept by NetAffiliation. You will receive payment at the end of the month via your chosen method of payment.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much does it cost?</p>
                <p class="answer"><span>A.</span>Webmaster registration is ENTIRELY FREE OF CHARGE! Affiliation is for you to make money, not spend it!</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much will I earn?</p>
                <p class="answer"><span>A.</span>That depends on each campaign.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How do I collect my earnings?</p>
                <p class="answer"><span>A.</span>NetAffiliation will collect your earnings from the advertisers and pay them to you every month once they reach a certain threshold. This threshold depends on the currency of payment.</p>
                </li>
                </ul>
                </div>
                <div class="faq-block"><a name="sgain"></a>
                <h4>Subscription Gain<a class="top" href="#top">Top</a></h4>
                <ul>
                <li>
                <p class="question"><span>Q.</span>Is joining Great-Deals free?</p>
                <p class="answer"><span>A.</span>Yes, and you also get 3 Euros bonus on joining.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>What is automatic invoicing?</p>
                <p class="answer"><span>A.</span>This service is only available to individuals. It enables our system to automatically create your invoice for the past month without you having to do anything: you won\'t receive any invoice requests and won\'t have to send us an invoice. A copy of the invoice will be sent to you by e-mail (you should keep it as proof) and another copy will be kept by NetAffiliation. You will receive payment at the end of the month via your chosen method of payment.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much does it cost?</p>
                <p class="answer"><span>A.</span>Webmaster registration is ENTIRELY FREE OF CHARGE! Affiliation is for you to make money, not spend it!</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much will I earn?</p>
                <p class="answer"><span>A.</span>That depends on each campaign.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How do I collect my earnings?</p>
                <p class="answer"><span>A.</span>NetAffiliation will collect your earnings from the advertisers and pay them to you every month once they reach a certain threshold. This threshold depends on the currency of payment.</p>
                </li>
                </ul>
                </div>
                <div class="faq-block"><a name="full-reimbru"></a>
                <h4>Full Reimbruisement<a class="top" href="#top">Top</a></h4>
                <ul>
                <li>
                <p class="question"><span>Q.</span>Is joining Great-Deals free?</p>
                <p class="answer"><span>A.</span>Yes, and you also get 3 Euros bonus on joining.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>What is automatic invoicing?</p>
                <p class="answer"><span>A.</span>This service is only available to individuals. It enables our system to automatically create your invoice for the past month without you having to do anything: you won\'t receive any invoice requests and won\'t have to send us an invoice. A copy of the invoice will be sent to you by e-mail (you should keep it as proof) and another copy will be kept by NetAffiliation. You will receive payment at the end of the month via your chosen method of payment.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much does it cost?</p>
                <p class="answer"><span>A.</span>Webmaster registration is ENTIRELY FREE OF CHARGE! Affiliation is for you to make money, not spend it!</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How much will I earn?</p>
                <p class="answer"><span>A.</span>That depends on each campaign.</p>
                </li>
                <li>
                <p class="question"><span>Q.</span>How do I collect my earnings?</p>
                <p class="answer"><span>A.</span>NetAffiliation will collect your earnings from the advertisers and pay them to you every month once they reach a certain threshold. This threshold depends on the currency of payment.</p>
                </li>
                </ul>
                </div><!-- testing -->
        
        ');
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
        $tnc->setName("Conso-Acteur");
        $tnc->setSlug("terms-and-conditions");
        $tnc->setCreatedAt(new \DateTime());
        $tnc->setUpdatedAt(new \DateTime());
        $tnc->setMetaTags("tnc");
        $tnc->setMetaDescription("ConsoActeur");
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
