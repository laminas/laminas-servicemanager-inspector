<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Command;

use Laminas\ServiceManager\Inspector\Command\InspectCommand;
use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfig;
use Laminas\ServiceManager\Inspector\DependencyConfig\LaminasDependecyConfigFactory;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Laminas\ServiceManager\Inspector\Scanner\DependencyScannerInterface;
use Laminas\ServiceManager\Inspector\Traverser\TraverserInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Laminas\ServiceManager\Inspector\Command\InspectCommand
 */
class InspectCommandTest extends TestCase
{
    use ProphecyTrait;

    public function testBeginsAnalysisWhenScannableDependenciesAreProvided(): void
    {
        $config = new DependencyConfig([
            'factories' => [
                'service'  => LaminasDependecyConfigFactory::class,
                'service2' => LaminasDependecyConfigFactory::class,
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->canScan(Argument::any())->willReturn(true);

        $traverser = $this->prophesize(TraverserInterface::class);
        $traverser->__invoke(Argument::type(Dependency::class))->shouldBeCalled(2);

        $eventCollector = $this->prophesize(EventCollectorInterface::class);
        $eventCollector->release(Argument::type(OutputInterface::class))->shouldBeCalled();

        $command = new InspectCommand(
            $config,
            $scanner->reveal(),
            $traverser->reveal(),
            $eventCollector->reveal(),
        );

        $command->run(
            $this->prophesize(InputInterface::class)->reveal(),
            $this->prophesize(OutputInterface::class)->reveal(),
        );
    }

    public function testSkipAnalysisWhenNonScannableDependenciesAreProvided(): void
    {
        $config = new DependencyConfig([
            'factories' => [
                'service'  => LaminasDependecyConfigFactory::class,
                'service2' => LaminasDependecyConfigFactory::class,
            ],
        ]);

        $scanner = $this->prophesize(DependencyScannerInterface::class);
        $scanner->canScan(Argument::any())->willReturn(false);

        $traverser = $this->prophesize(TraverserInterface::class);
        $traverser->__invoke(Argument::type(Dependency::class))->shouldNotBeCalled();

        $eventCollector = $this->prophesize(EventCollectorInterface::class);
        $eventCollector->release(Argument::type(OutputInterface::class))->shouldBeCalled();

        $command = new InspectCommand(
            $config,
            $scanner->reveal(),
            $traverser->reveal(),
            $eventCollector->reveal(),
        );

        $command->run(
            $this->prophesize(InputInterface::class)->reveal(),
            $this->prophesize(OutputInterface::class)->reveal(),
        );
    }
}
