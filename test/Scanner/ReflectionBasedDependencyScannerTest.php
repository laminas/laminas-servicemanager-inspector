<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Scanner;

use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector;
use Laminas\ServiceManager\Inspector\Scanner\ReflectionBasedDependencyScanner;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Laminas\ServiceManager\Inspector\Scanner\ReflectionBasedDependencyScanner
 */
class ReflectionBasedDependencyScannerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @dataProvider providerSupportedFactories
     */
    public function testCanScanWhenSupportedFactoriesAreProvided(string $factory)
    {
        $config = new DependencyConfig([
            'factories' => [
                'a' => $factory,
            ],
        ]);

        $scanner = new ReflectionBasedDependencyScanner(
            $config,
            new NullEventCollector()
        );

        $this->assertTrue($scanner->canScan('a'));
    }

    public function providerSupportedFactories(): array
    {
        return [
            'LaminasReflectionBasedAbstractFactory' => ['Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory'],
            'ZendReflectionBasedAbstractFactory' => ['Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory'],
        ];
    }
}
