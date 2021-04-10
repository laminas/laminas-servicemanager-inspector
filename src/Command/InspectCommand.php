<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Command;

use Laminas\ServiceManager\Inspector\Analyzer\FactoryAnalyzerInterface;
use Laminas\ServiceManager\Inspector\DependencyConfig;
use Laminas\ServiceManager\Inspector\Traverser\Dependency;
use Laminas\ServiceManager\Inspector\Traverser\Traverser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class InspectCommand extends Command
{
    public const HELP = <<<'EOH'
TBD
EOH;

    /** @var string $defaultName */
    public static $defaultName = 'servicemanager:inspect';

    /** @var DependencyConfig */
    private $config;

    /** @var FactoryAnalyzerInterface */
    private $factoryAnalyzer;

    /** @var Traverser */
    private $traverser;

    public function __construct(
        DependencyConfig $config,
        FactoryAnalyzerInterface $factoryAnalyzer,
        Traverser $traverser
    ) {
        $this->config          = $config;
        $this->factoryAnalyzer = $factoryAnalyzer;
        $this->traverser       = $traverser;

        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this->setDescription('ServiceManager inspector');
        $this->setHelp(self::HELP);
    }

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->config->getFactories() as $serviceName => $factoryClass) {
            if ($this->factoryAnalyzer->canDetect($serviceName)) {
                // TODO don't fail here - collect all errors
                ($this->traverser)(new Dependency($serviceName));
            }
        }

        return 0;
    }
}
