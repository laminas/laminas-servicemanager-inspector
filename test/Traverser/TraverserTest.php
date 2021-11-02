<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfig;
use Laminas\ServiceManager\Inspector\DependencyConfig\LaminasDependecyConfigFactory;
use Laminas\ServiceManager\Inspector\Event\AutowireFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\CircularDependencyDetectedEvent;
use Laminas\ServiceManager\Inspector\Event\CustomFactoryEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\InvokableEnteredEvent;
use Laminas\ServiceManager\Inspector\Event\MissingFactoryDetectedEvent;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Laminas\ServiceManager\Inspector\Scanner\DependencyScannerInterface;
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
        $config = new DependencyConfig([
            'invokables' => [
                'a' => Dependency::class,
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->__invoke(Argument::type(InvokableEnteredEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config,
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

    public function testEmitsAutowireFactoryEventWhenDependencyWithAutowireFactoryIsProvided()
    {
        $config = new DependencyConfig([
            'factories' => [
                'a' => ReflectionBasedAbstractFactory::class,
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->__invoke(Argument::type(AutowireFactoryEnteredEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config,
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

    public function testEmitsCustomFactoryEventWhenDependencyWithCustomFactoryIsProvided()
    {
        $config = new DependencyConfig([
            'factories' => [
                'a' => LaminasDependecyConfigFactory::class,
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->__invoke(Argument::type(CustomFactoryEnteredEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config,
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

    public function testEmitsNoEventsWhenTravelIntoOptionalDependency()
    {
        $config = new DependencyConfig([]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->__invoke(Argument::type(MissingFactoryDetectedEvent::class))->shouldNotBeCalled();

        $traverser = new Traverser(
            $config,
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a', true));
    }

    public function testEmitsMissingFactoryEventWhenTravelIntoDependencyWithoutFactory()
    {
        $config = new DependencyConfig([]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->__invoke(Argument::type(MissingFactoryDetectedEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config,
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }

    public function testEmitsCircularDependencyEventWhenCircularDependencyIsProvided()
    {
        $config = new DependencyConfig([
            'factories' => [
                'a' => ReflectionBasedAbstractFactory::class,
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->scan(Argument::type('string'))->willReturn([new Dependency('a')]);

        $events = $this->prophesize(EventCollectorInterface::class);
        $events->__invoke(Argument::type(AutowireFactoryEnteredEvent::class))->shouldBeCalled();
        $events->__invoke(Argument::type(CircularDependencyDetectedEvent::class))->shouldBeCalled();

        $traverser = new Traverser(
            $config,
            $scanner->reveal(),
            $events->reveal(),
        );

        $traverser(new Dependency('a'));
    }
}
