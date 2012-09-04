<?php


//require __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/ApcUniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

//use Symfony\Component\ClassLoader\ApcUniversalClassLoader;

//$loader = new ApcUniversalClassLoader('GD_APC_UniversalClassLoader_');
$loader = new UniversalClassLoader();
// TODO: Remove Liugio Excel Bundle
$loader->registerNamespaces(array(
    'Symfony'           => array(__DIR__ . '/../vendor/symfony/src', __DIR__ . '/../vendor/bundles'),
    'Sensio'            => __DIR__ . '/../vendor/bundles',
    'JMS'              => __DIR__.'/../vendor/bundles',
    'Metadata'         => __DIR__.'/../vendor/metadata/src',
    'CG'               => __DIR__.'/../vendor/cg-library/src',
    'Doctrine\\Bundle'  => __DIR__ . '/../vendor/bundles',
    'Doctrine\\Common\\DataFixtures' => __DIR__. '/../vendor/doctrine-fixtures/lib',
    'Doctrine\\Common'  => __DIR__ . '/../vendor/doctrine-common/lib',
    'Doctrine\\DBAL'    => __DIR__ . '/../vendor/doctrine-dbal/lib',
    'Doctrine'          => __DIR__ . '/../vendor/doctrine/lib',
    'Monolog'           => __DIR__ . '/../vendor/monolog/src',
    'Assetic'           => __DIR__ . '/../vendor/assetic/src',
    'Stof'              => __DIR__.'/../vendor/bundles',
    'Gedmo'             => __DIR__.'/../vendor/gedmo-doctrine-extensions/lib',
    'FOS'               => __DIR__.'/../vendor/bundles',
    'Sonata'     => __DIR__.'/../vendor/bundles',
    'Exporter'   => __DIR__.'/../vendor/exporter/lib',
    'Knp\Bundle' => __DIR__.'/../vendor/bundles',
    'Knp\Menu'   => __DIR__.'/../vendor/knp/menu/src',
    'Knp\\Component'      => __DIR__.'/../vendor/knp-components/src',
    'Knp\\Bundle'         => __DIR__.'/../vendor/bundles',
    'WhiteOctober'      =>__DIR__.'/../vendor/bundles',
    'n3b\\Bundle\\Util\\HttpFoundation\\StreamResponse'  => __DIR__.'/../vendor/n3b/src',
    'liuggio'              => __DIR__.'/../vendor/bundles',
    'Imagine'   => __DIR__.'/../vendor/imagine/lib',
    'Liip'      => __DIR__.'/../vendor/bundles',
));
$loader->registerPrefixes(array(
    'Twig_Extensions_' => __DIR__ . '/../vendor/twig-extensions/lib',
    'Twig_' => __DIR__ . '/../vendor/twig/lib',
    'Zend' => __DIR__.'/../src/Zend',
    'PHPExcel'         => __DIR__.'/../vendor/phpexcel/lib/PHPExcel/Classes',
));

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__ . '/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->registerPrefixFallbacks(array(__DIR__ . '/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs'));
}

$loader->registerNamespaceFallbacks(array(
    __DIR__ . '/../src',
));
$loader->register();

AnnotationRegistry::registerLoader(function($class) use ($loader)
{
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerFile(__DIR__ . '/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

// Swiftmailer needs a special autoloader to allow
// the lazy loading of the init file (which is expensive)
require_once __DIR__ . '/../vendor/swiftmailer/lib/classes/Swift.php';
Swift::registerAutoload(__DIR__ . '/../vendor/swiftmailer/lib/swift_init.php');
