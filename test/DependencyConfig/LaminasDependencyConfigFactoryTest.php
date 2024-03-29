<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\DependencyConfig;

use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfig;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\DependencyConfig\LaminasDependecyConfigFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\DependencyConfig\LaminasDependecyConfigFactory
 */
class LaminasDependencyConfigFactoryTest extends TestCase
{
    public function testConstructsServiceWhenContainerWithServiceManagerConfigKeyIsProvided()
    {
        $config    = [
            'service_manager' => [
                'factories' => [],
            ],
        ];
        $container = new ServiceManager();
        $container->setService('config', $config);

        $factory = new LaminasDependecyConfigFactory();
        $service = $factory($container, DependencyConfigInterface::class);

        $this->assertInstanceOf(DependencyConfig::class, $service);
    }
}
