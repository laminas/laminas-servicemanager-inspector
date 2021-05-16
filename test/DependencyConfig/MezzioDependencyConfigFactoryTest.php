<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\DependencyConfig;

use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfig;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\DependencyConfig\MezzioDependencyConfigFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\DependencyConfig\LaminasDependecyConfigFactory
 */
class MezzioDependencyConfigFactoryTest extends TestCase
{
    public function testConstructsServiceWhenContainerWithDependenciesConfigKeyIsProvided()
    {
        $config = [
            'dependencies' => [
                'factories' => [],
            ],
        ];
        $container = new ServiceManager();
        $container->setService('config', $config);

        $factory = new MezzioDependencyConfigFactory();
        $service = $factory($container, DependencyConfigInterface::class);

        $this->assertInstanceOf(DependencyConfig::class, $service);
    }
}