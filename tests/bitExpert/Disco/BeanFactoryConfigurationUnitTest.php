<?php

/*
 * This file is part of the Disco package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace bitExpert\Disco;

use bitExpert\Disco\Store\SerializableBeanStore;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\VoidCache;
use ProxyManager\Autoloader\Autoloader;
use ProxyManager\Autoloader\AutoloaderInterface;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\Inflector\ClassNameInflector;

/**
 * Unit test for {@link \bitExpert\Disco\BeanFactoryConfiguration}.
 */
class BeanFactoryConfigurationUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function invalidProxyTargetDirThrowsException()
    {
        new BeanFactoryConfiguration('/abc');
    }

    /**
     * @test
     */
    public function configuredProxyTargetDirCanBeRetrieved()
    {
        $config = new BeanFactoryConfiguration(sys_get_temp_dir());
        $proxyManagerConfig = $config->getProxyManagerConfiguration();

        self::assertSame(sys_get_temp_dir(), $proxyManagerConfig->getProxiesTargetDir());
    }

    /**
     * @test
     */
    public function defaultAnnotationCacheDirWillDefaultToProxyTargetDir()
    {
        $config = new BeanFactoryConfiguration(sys_get_temp_dir());

        /** @var FilesystemCache $annotationCache */
        $annotationCache = $config->getAnnotationCache();

        self::assertInstanceOf(FilesystemCache::class, $annotationCache);
        self::assertSame(sys_get_temp_dir(), $annotationCache->getDirectory());
    }

    /**
     * @test
     */
    public function customAnnotationCacheDirCanBeConfigured()
    {
        $config = new BeanFactoryConfiguration(sys_get_temp_dir());
        $config->setAnnotationCache(new FilesystemCache(__DIR__));

        /** @var FilesystemCache $annotationCache */
        $annotationCache = $config->getAnnotationCache();

        self::assertInstanceOf(FilesystemCache::class, $annotationCache);
        self::assertSame(__DIR__, $annotationCache->getDirectory());
    }

    /**
     * @test
     */
    public function configuredAnnotationCacheInstanceCanBeRetrieved()
    {
        $config = new BeanFactoryConfiguration(sys_get_temp_dir());
        $config->setAnnotationCache(new VoidCache());

        $annotationCache = $config->getAnnotationCache();

        self::assertInstanceOf(VoidCache::class, $annotationCache);
    }

    /**
     * @test
     */
    public function configuredGeneratorStrategyInstanceCanBeRetrieved()
    {
        $config = new BeanFactoryConfiguration(sys_get_temp_dir());
        $config->setProxyWriterGenerator(new EvaluatingGeneratorStrategy());

        $proxyManagerConfig = $config->getProxyManagerConfiguration();

        self::assertInstanceOf(EvaluatingGeneratorStrategy::class, $proxyManagerConfig->getGeneratorStrategy());
    }

    /**
     * @test
     */
    public function configuredProxyAutoloaderInstanceCanBeRetrieved()
    {
        $autoloader = $this->createMock(AutoloaderInterface::class);

        $config = new BeanFactoryConfiguration(sys_get_temp_dir());
        $config->setProxyAutoloader($autoloader);
        $proxyManagerConfig = $config->getProxyManagerConfiguration();

        self::assertSame($autoloader, $proxyManagerConfig->getProxyAutoloader());
    }

    /**
     * @test
     */
    public function enablingProxyAutoloaderRegistersAdditionalAutoloader()
    {
        $autoloader = new Autoloader(new FileLocator(sys_get_temp_dir()), new ClassNameInflector('AUTOLOADER'));

        $autoloaderFunctionsBeforeBeanFactoryInit = spl_autoload_functions();
        $beanFactoryConfig = new BeanFactoryConfiguration(sys_get_temp_dir());
        $beanFactoryConfig->setProxyAutoloader($autoloader);
        $autoloaderFunctionsAfterBeanFactoryInit = spl_autoload_functions();

        self::assertCount(
            count($autoloaderFunctionsBeforeBeanFactoryInit) + 1,
            $autoloaderFunctionsAfterBeanFactoryInit
        );
    }

    /**
     * @test
     */
    public function configuredBeanStoreInstanceCanBererieved()
    {
        $beanStore = new SerializableBeanStore();

        $config = new BeanFactoryConfiguration(sys_get_temp_dir());
        $config->setBeanStore($beanStore);

        self::assertSame($beanStore, $config->getBeanStore());
    }
}
