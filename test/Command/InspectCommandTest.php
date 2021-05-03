<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Command;

use Laminas\ServiceManager\Inspector\Analyzer\FactoryAnalyzerInterface;
use Laminas\ServiceManager\Inspector\Command\InspectCommand;
use Laminas\ServiceManager\Inspector\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\Traverser\Dependency;
use Laminas\ServiceManager\Inspector\Traverser\TraverserInterface;
use Laminas\ServiceManager\Inspector\Visitor\ConsoleStatsVisitor;
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

    public function testExecute_DetectableFactoriesAreProvided_BeginsAnalysis(): void
    {
        $config = $this->prophesize(DependencyConfigInterface::class);
        $config->getFactories()->shouldBeCalled()->willReturn([
            'service' => 'factory1',
            'service2' => 'factory2',
        ]);

        $analyzer = $this->prophesize(FactoryAnalyzerInterface::class);
        $analyzer->canDetect(Argument::any())->willReturn(true);

        $traverser = $this->prophesize(TraverserInterface::class);
        $traverser->setVisitor(Argument::type(ConsoleStatsVisitor::class))->shouldBeCalled();
        $traverser->__invoke(Argument::type(Dependency::class))->shouldBeCalled(2);

        $command = new InspectCommand(
            $config->reveal(),
            $analyzer->reveal(),
            $traverser->reveal(),
        );

        $command->run(
            $this->prophesize(InputInterface::class)->reveal(),
            $this->prophesize(OutputInterface::class)->reveal(),
        );
    }

}