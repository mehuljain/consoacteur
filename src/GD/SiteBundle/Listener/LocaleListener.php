<?php

namespace GD\SiteBundle\Listener;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\Container;

class LocaleListener
{
    protected $router;
    protected $languages;
    protected $fallbackLanguage;

    private function getPermissibleLocales()
    {
        foreach($this->languages as $language){
            $result[] = $language['locale_name'];
        }
        return $result;
    }

    private function replaceIncorrectLocaleInUri($locale)
    {
        $permissibleLocales = $this->getPermissibleLocales();
        if(!in_array($locale, $permissibleLocales)) {
            $locale = $this->fallbackLanguage;
        }
        return $locale;
    }

    protected $uri = array(
        '/' => '/{_locale}',
        '/app.php' => '/app.php/{_locale}',
        '/app_dev.php' => '/app_dev.php/{_locale}',
        '/admin/dashboard' => '/{_locale}/admin/dashboard',
        '/app.php/admin/dashboard' => '/{_locale}/admin/dashboard',
        '/app_dev.php/admin/dashboard' => '/app_dev.php/{_locale}/admin/dashboard',
    );

    public function __construct(\Symfony\Component\Routing\Router $router, \Symfony\Component\DependencyInjection\Container $container, $languages, $fallbackLanguage)
    {
        $this->router = $router;
        $this->languages = $languages;
        $this->fallbackLanguage = $fallbackLanguage;
    }

    /**
     * This method is executed on each request and checks for the existence of _locale in the URI. If not present it redirects to one with _locale. It also uses the fallback_locale config variable to set the locale in case of a wrong input. 
     *
     * @param \Symfony\Component\EventDispatcher\Event $event
     */
    public function onKernelRequest(Event $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        $requestUri = substr($request->getRequestUri(), -1) == '/' ? substr($request->getRequestUri(), 0, strlen($request->getRequestUri()) - 1) : $request->getRequestUri();
        if (array_key_exists($requestUri, $this->uri)) {
            $locale = $request->getSession()->getLocale();
            $redirectTo = str_replace('{_locale}', $locale, $this->uri[$requestUri]);
            $event->setResponse(new RedirectResponse($redirectTo));
        } else {
            $routeParams = $this->router->match($request->getPathInfo()); 
            $routeName = $routeParams['_route'];
            if ($routeName[0] == '_' || $routeName == 'fos_js_routing_js') {
                return;
            }
            unset($routeParams['_route']);

            //Replace bad locales in URL with fallback_locale
            if(in_array('_locale', array_keys($routeParams))){
                $locale = $this->replaceIncorrectLocaleInUri($routeParams['_locale']);
                $routeParams['_locale'] = $locale;
                $session->setLocale($locale);
            } elseif (in_array('locale', array_keys($routeParams))){ //TODO: discover where _locale is being changed to locale
                $locale = $this->replaceIncorrectLocaleInUri($routeParams['locale']);
                $routeParams['locale'] = $locale;
                $session->setLocale($locale);
            }

            $routeData = array('name' => $routeName, 'params' => $routeParams);

            //Skipping duplicates
            $thisRoute = $session->get('this_route', array());
            if ($thisRoute == $routeData) {
                return;
            }

            $session->set('last_route', $thisRoute);
            $session->set('this_route', $routeData);
        }
    }
}
