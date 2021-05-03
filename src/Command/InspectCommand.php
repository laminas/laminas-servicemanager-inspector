<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Command;

use Laminas\ServiceManager\Inspector\Analyzer\FactoryAnalyzerInterface;
use Laminas\ServiceManager\Inspector\DependencyConfigInterface;
use Laminas\ServiceManager\Inspector\Traverser\Dependency;
use Laminas\ServiceManager\Inspector\Traverser\TraverserInterface;
use Laminas\ServiceManager\Inspector\Visitor\ConsoleStatsVisitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class InspectCommand extends Command
{
    public const HELP = <<<'EOH'
TODO
EOH;

    /** @var string|null $defaultName */
    public static $defaultName = 'servicemanager:inspect';

    /** @var DependencyConfigInterface */
    private $config;

    /** @var FactoryAnalyzerInterface */
    private $factoryAnalyzer;

    /** @var TraverserInterface */
    private $traverser;

    public function __construct(
        DependencyConfigInterface $config,
        FactoryAnalyzerInterface $factoryAnalyzer,
        TraverserInterface $traverser
    ) {
        $this->config          = $config;
        $this->factoryAnalyzer = $factoryAnalyzer;
        $this->traverser       = $traverser;

        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this->setDescription('ServiceManager inspector');
        $this->setHelp(self::HELP);
    }

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $visitor = new ConsoleStatsVisitor($output);
        $this->traverser->setVisitor($visitor);

        foreach ($this->config->getFactories() as $serviceName => $factoryClass) {
            if ($this->factoryAnalyzer->canDetect($serviceName)) {
                // TODO don't fail here - collect all occurring errors
                ($this->traverser)(new Dependency($serviceName));
            }
        }

        $visitor->render();

        return 0;
    }
}
