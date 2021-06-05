<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\DependencyConfig;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfig;
use Laminas\ServiceManager\Inspector\Event\AutoloadProblemDetectedEvent;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Zakirullin\Mess\Exception\UnexpectedTypeException;

/**
 * @covers \Laminas\ServiceManager\Inspector\DependencyConfig
 */
class DependencyConfigTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @doesNotPerformAssertions
     */
    public function testThrowsNoExceptionOnEmptyDependencies()
    {
        new DependencyConfig([]);
    }

    public function testThrowsExceptionOnInvalidFactories()
    {
        $this->expectException(UnexpectedTypeException::class);

        new DependencyConfig([
            'factories' => ['a'],
        ]);
    }

    public function testThrowsExceptionOnInvalidInvokables()
    {
        $this->expectException(UnexpectedTypeException::class);

        new DependencyConfig([
            'invokables' => ['a'],
        ]);
    }

    public function testThrowsExceptionOnInvalidAliases()
    {
        $this->expectException(UnexpectedTypeException::class);

        new DependencyConfig([
            'aliases' => ['a'],
        ]);
    }

    public function testReturnsSameFactoriesWhenValidFactoriesAreProvided()
    {
        $depenencies = [
            'factories' => [
                'service1' => ReflectionBasedAbstractFactory::class,
            ],
        ];

        $config = new DependencyConfig($depenencies);

        $this->assertSame(
            [
                'service1' => ReflectionBasedAbstractFactory::class,
            ],
            $config->getFactories()
        );
    }

    public function testFiresAutoloadProblemEventWhenAutoloadProblemOccurs()
    {
        $depenencies = [
            'factories' => [
                'service1' => 'unresolvable',
            ],
        ];

        $config = new DependencyConfig($depenencies);

        $eventCollector = $this->prophesize(EventCollectorInterface::class);
        $eventCollector->collect(Argument::type(AutoloadProblemDetectedEvent::class))->shouldBeCalled();

        $config->releaseEvents($eventCollector->reveal());
    }
}
