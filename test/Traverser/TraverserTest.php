<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\Event\AutowireFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\CircularDependencyDetectedEvent;
use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\InvokableEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\MissingFactoryDetectedEvent;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector;
use Laminas\ServiceManager\Inspector\LaminasDependecyConfigFactory;
use Laminas\ServiceManager\Inspector\Scanner\DependencyScannerInterface;
use Laminas\ServiceManager\Inspector\Traverser\Dependency;
use Laminas\ServiceManager\Inspector\Traverser\Traverser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Laminas\ServiceManager\Inspector\Traverser\Traverser
 */
class TraverserTest extends TestCase
{
    use ProphecyTrait;

    public function testEmitsInvokableEventWhenInvokableDependencyIsProvided()
    {
        $config = new DependencyConfig(new NullEventCollector(), [
            'invokables' => [
                'a' => Dependency::class,
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->collect(Argument::type(InvokableEnteredEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config,
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

    public function testEmitsAutowireFactoryEventWhenDependencyWithAutowireFactoryIsProvided()
    {
        $config = new DependencyConfig(new NullEventCollector(), [
            'factories' => [
                'a' => 'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->collect(Argument::type(AutowireFactoryEnteredEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config->reveal(),
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

    public function testEmitsCustomFactoryEventWhenDependencyWithCustomFactoryIsProvided()
    {
        $config = new DependencyConfig(new NullEventCollector(), [
            'factories' => [
                'a' => LaminasDependecyConfigFactory::class,
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->collect(Argument::type(CustomFactoryEnteredEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config->reveal(),
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

    public function testEmitsNoEventsWhenTravelIntoOptionalDependency()
    {
        $config = $this->prophesize(DependencyConfigInterface::class);
        $config->isInvokable(Argument::type('string'))->willReturn(false);
        $config->hasAutowireFactory(Argument::type('string'))->willReturn(false);
        $config->hasFactory(Argument::type('string'))->willReturn(false);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->collect(Argument::type(MissingFactoryDetectedEvent::class))->shouldNotBeCalled();

        $traverser = new Traverser(
            $config->reveal(),
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a', true));
    }

    public function testEmitsMissingFactoryEventWhenTravelIntoDependencyWithoutFactory()
    {
        $config = $this->prophesize(DependencyConfigInterface::class);
        $config->isInvokable(Argument::type('string'))->willReturn(false);
        $config->hasAutowireFactory(Argument::type('string'))->willReturn(false);
        $config->hasFactory(Argument::type('string'))->willReturn(false);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->collect(Argument::type(MissingFactoryDetectedEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config->reveal(),
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

    public function testEmitsCircularDependencyEventWhenCircularDependencyIsProvided()
    {
        $config = $this->prophesize(DependencyConfigInterface::class);
        $config->isInvokable(Argument::type('string'))->willReturn(false);
        $config->hasAutowireFactory(Argument::type('string'))->willReturn(false);
        $config->hasFactory(Argument::type('string'))->willReturn(true);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([new Dependency('a')]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->collect(Argument::type(CustomFactoryEnteredEvent::class))->shouldBeCalled();
        $events->collect(Argument::type(CircularDependencyDetectedEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config->reveal(),
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

}