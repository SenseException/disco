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

use bitExpert\Disco\Helper\BeanFactoryAwareService;
use ReflectionProperty;

/**
 * Unit test for {@link \bitExpert\Disco\BeanFactoryPostProcessor}.
 */
class BeanFactoryPostProcessorUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function notDefinedRegistryWillBeIgnoredByBeanFactoryPostProcessor()
    {
        $processor = new BeanFactoryPostProcessor();
        $service = new BeanFactoryAwareService();

        // un-setting the BeanFactory instance stored in the BeanFactoryRegistry
        $reflection = new ReflectionProperty(\bitExpert\Disco\BeanFactoryRegistry::class, 'beanFactory');
        $reflection->setAccessible(true);
        $reflection->setValue(null);

        $processor->postProcess($service, 'BeanFactoryAwareService');
        $this->assertNull($service->getBeanFactory());
    }
}
