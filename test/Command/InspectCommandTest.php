<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Command;

use Laminas\ServiceManager\Inspector\Command\InspectCommand;
use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use Laminas\ServiceManager\Inspector\DependencyConfig\DependencyConfig;
use Laminas\ServiceManager\Inspector\DependencyConfig\LaminasDependecyConfigFactory;
use Laminas\ServiceManager\Inspector\EventCollector\NullEventCollector;
use Laminas\ServiceManager\Inspector\EventReporter\NullEventReporter;
use Laminas\ServiceManager\Inspector\Scanner\DependencyScannerInterface;
use Laminas\ServiceManager\Inspector\Traverser\TraverserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Laminas\ServiceManager\Inspector\Command\InspectCommand
 */
class InspectCommandTest extends TestCase
{
    public function testBeginsAnalysisWhenScannableDependenciesAreProvided(): void
    {
        $config = new DependencyConfig([
            'factories' => [
                'service'  => LaminasDependecyConfigFactory::class,
                'service2' => LaminasDependecyConfigFactory::class,
            ],
        ]);
        /** @var DependencyScannerInterface|MockObject $scanner  */
        $scanner = $this->createMock(DependencyScannerInterface::class);
        $scanner->method('canScan')
            ->withAnyParameters()
            ->willReturn(true);

        /** @var TraverserInterface|MockObject $traverser  */
        $traverser = $this->createMock(TraverserInterface::class);
        $traverser->expects($this->exactly(2))
            ->method('__invoke')
            ->with($this->isInstanceOf(Dependency::class));

        $command = new InspectCommand(
            $config,
            $scanner,
            $traverser,
            new NullEventCollector(),
            new NullEventReporter(),
        );

        $command->run(
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
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

        /** @var DependencyScannerInterface|MockObject $scanner  */
        $scanner = $this->createMock(DependencyScannerInterface::class);
        $scanner->method('canScan')->withAnyParameters()->willReturn(false);

        /** @var TraverserInterface|MockObject $traverser  */
        $traverser = $this->createMock(TraverserInterface::class);
        $traverser->expects($this->never())
            ->method('__invoke')
            ->with($this->isInstanceOf(Dependency::class));

        $command = new InspectCommand(
            $config,
            $scanner,
            $traverser,
            new NullEventCollector(),
            new NullEventReporter(),
        );

        $command->run(
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );
    }
}
