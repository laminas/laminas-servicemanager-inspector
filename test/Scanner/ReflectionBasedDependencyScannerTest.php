<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Scanner;

use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfig;
use Laminas\ServiceManager\Inspector\Event\UnresolvableParameterDetectedEvent;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector;
use Laminas\ServiceManager\Inspector\Scanner\ReflectionBasedDependencyScanner;
use LaminasTest\ServiceManager\Inspector\Scanner\Stub\ClassWithExistingClassParameter;
use LaminasTest\ServiceManager\Inspector\Scanner\Stub\ClassWithNonExistingClassParameter;
use LaminasTest\ServiceManager\Inspector\Scanner\Stub\ClassWithOptionalNonExistingClassParameter;
use LaminasTest\ServiceManager\Inspector\Scanner\Stub\ClassWithOptionalScalarParameter;
use LaminasTest\ServiceManager\Inspector\Scanner\Stub\ClassWithScalarParameter;
use LaminasTest\ServiceManager\Inspector\Scanner\Stub\ClassWithScalarParameterWithDefaultValue;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;

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
        $config = new DependencyConfig(
            [
                'factories' => [
                    'a' => $factory,
                ],
            ]
        );

        $scanner = new ReflectionBasedDependencyScanner(
            $config,
            new NullEventCollector()
        );

        $this->assertTrue($scanner->canScan('a'));
    }

    public function providerSupportedFactories(): array
    {
        return [
            'LaminasReflectionBasedAbstractFactory' => [
                // phpcs:ignore
                'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory'
            ],
            'ZendReflectionBasedAbstractFactory'    => [
                // phpcs:ignore
                'Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory'
            ],
        ];
    }

    public function testDetectsZeroDependenciesOnEmptyConstructor()
    {
        $scanner = new ReflectionBasedDependencyScanner(
            new DependencyConfig([]),
            new NullEventCollector(),
        );

        $dependencies = $scanner->scan(stdClass::class);

        $this->assertEmpty($dependencies);
    }

    public function testDetectsAllDependenciesRequiredInConstructor()
    {
        $config = new DependencyConfig(
            [
                'factories' => [
                    ClassWithExistingClassParameter::class
                    // phpcs:ignore
                    => 'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
                ],
            ]
        );

        $scanner = new ReflectionBasedDependencyScanner(
            $config,
            new NullEventCollector(),
        );

        $dependencies = $scanner->scan(ClassWithExistingClassParameter::class);

        $this->assertArrayHasKey(0, $dependencies);
        $this->assertSame(stdClass::class, $dependencies[0]->getName());
        $this->assertFalse($dependencies[0]->isOptional());
    }

    public function testFiresUnresolvableParameterEventOnScalarInConstructor()
    {
        $config = new DependencyConfig(
            [
                'factories' => [
                    ClassWithScalarParameter::class
                    // phpcs:ignore
                    => 'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
                ],
            ]
        );

        $eventCollector = $this->prophesize(EventCollectorInterface::class);
        $eventCollector->__invoke(Argument::type(UnresolvableParameterDetectedEvent::class))->shouldBeCalled();

        $scanner = new ReflectionBasedDependencyScanner(
            $config,
            $eventCollector->reveal(),
        );

        $dependencies = $scanner->scan(ClassWithScalarParameter::class);

        $this->assertEmpty($dependencies);
    }

    public function testFiresNoEventsOnOptionalScalarInConstructor()
    {
        $config = new DependencyConfig(
            [
                'factories' => [
                    ClassWithOptionalScalarParameter::class
                    // phpcs:ignore
                    => 'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
                ],
            ]
        );

        $eventCollector = $this->prophesize(EventCollectorInterface::class);
        $eventCollector->__invoke(Argument::type(UnresolvableParameterDetectedEvent::class))->shouldBeCalled();

        $scanner = new ReflectionBasedDependencyScanner(
            $config,
            $eventCollector->reveal(),
        );

        $dependencies = $scanner->scan(ClassWithOptionalScalarParameter::class);

        $this->assertEmpty($dependencies);
    }

    public function testFiresNoEventsOnScalarWithDefaultValueInConstructor()
    {
        $config = new DependencyConfig(
            [
                'factories' => [
                    ClassWithScalarParameterWithDefaultValue::class
                    // phpcs:ignore
                    => 'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
                ],
            ]
        );

        $eventCollector = $this->prophesize(EventCollectorInterface::class);
        $eventCollector->__invoke(Argument::any())->shouldNotBeCalled();

        $scanner = new ReflectionBasedDependencyScanner(
            $config,
            $eventCollector->reveal(),
        );

        $dependencies = $scanner->scan(ClassWithScalarParameterWithDefaultValue::class);

        $this->assertEmpty($dependencies);
    }

    public function testFiresUnresolvableParameterEventOnNonExistingClassParameterInConstructor()
    {
        $config = new DependencyConfig(
            [
                'factories' => [
                    ClassWithNonExistingClassParameter::class
                    // phpcs:ignore
                    => 'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
                ],
            ]
        );

        $eventCollector = $this->prophesize(EventCollectorInterface::class);
        $eventCollector->__invoke(Argument::type(UnresolvableParameterDetectedEvent::class))->shouldBeCalled();

        $scanner = new ReflectionBasedDependencyScanner(
            $config,
            $eventCollector->reveal(),
        );

        $dependencies = $scanner->scan(ClassWithNonExistingClassParameter::class);

        $this->assertEmpty($dependencies);
    }

    public function testFiresNoEventsOnOptionalNonExistingClassParameterInConstructor()
    {
        $config = new DependencyConfig(
            [
                'factories' => [
                    ClassWithOptionalNonExistingClassParameter::class
                    // phpcs:ignore
                    => 'Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory',
                ],
            ]
        );

        $eventCollector = $this->prophesize(EventCollectorInterface::class);
        $eventCollector->__invoke(Argument::any())->shouldNotBeCalled();

        $scanner = new ReflectionBasedDependencyScanner(
            $config,
            $eventCollector->reveal(),
        );

        $dependencies = $scanner->scan(ClassWithNonExistingClassParameter::class);

        $this->assertEmpty($dependencies);
    }
}
