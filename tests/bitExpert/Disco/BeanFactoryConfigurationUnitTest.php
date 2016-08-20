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

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\VoidCache;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;

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

        self::assertSame(sys_get_temp_dir(), $config->getProxyTargetDir());
    }

    /**
     * @test
     */
    public function annotationCacheWillDefaultToProxyTargetDir()
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
    public function customAnnotationCacheInstanceCanBeRetrieved()
    {
        $config = new BeanFactoryConfiguration(sys_get_temp_dir(), null, new VoidCache());

        $annotationCache = $config->getAnnotationCache();
        self::assertInstanceOf(VoidCache::class, $annotationCache);
    }

    /**
     * @test
     */
    public function customGeneratorStrategyInstanceCanBeRetrieved()
    {
        $config = new BeanFactoryConfiguration(sys_get_temp_dir(), new EvaluatingGeneratorStrategy());

        $proxyGeneratorStrategy = $config->getProxyGeneratorStrategy();
        self::assertInstanceOf(EvaluatingGeneratorStrategy::class, $proxyGeneratorStrategy);
    }

    /**
     * @test
     * @dataProvider proxyAutoloaderFlags
     */
    public function configuredProxyAutoloaderFlagCanBeRetrieved(bool $autoloadConfig)
    {
        $config = new BeanFactoryConfiguration(sys_get_temp_dir(), null, null, $autoloadConfig);

        self::assertSame($autoloadConfig, $config->useProxyAutoloader());
    }

    public function proxyAutoloaderFlags()
    {
        return [
          [ true ],
          [ false ]
        ];
    }
}
