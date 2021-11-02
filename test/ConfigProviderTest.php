<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector;

use Laminas\ServiceManager\Inspector\Command\InspectCommand;
use Laminas\ServiceManager\Inspector\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Laminas\ServiceManager\Inspector\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testProviderHasExpectedTopLevelKeys(): void
    {
        $config = (new ConfigProvider())();

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey('laminas-cli', $config);
    }

    public function expectedCommandFactoryKeys(): array
    {
        return [
            'InspectCommand' => [InspectCommand::class, 'servicemanager:inspect'],
        ];
    }

    /**
     * @dataProvider expectedCommandFactoryKeys
     */
    public function testDependenciesIncludesFactoriesForEachCommand(string $commandClass): void
    {
        $config = (new ConfigProvider())();
        $this->assertArrayHasKey('dependencies', $config);

        $dependencies = $config['dependencies'];
        $this->assertArrayHasKey('factories', $dependencies);

        $factories = $dependencies['factories'];
        $this->assertArrayHasKey($commandClass, $factories);
    }

    /**
     * @dataProvider expectedCommandFactoryKeys
     */
    public function tesCommandNamesToCommandClasses(string $commandClass, string $command): void
    {
        $config = (new ConfigProvider())();
        $this->assertArrayHasKey('laminas-cli', $config);

        $cliConfig = $config['laminas-cli'];
        $this->assertArrayHasKey('commands', $cliConfig);

        $commands = $cliConfig['commands'];
        $this->assertArrayHasKey($command, $commands);
        $this->assertSame($commandClass, $commands[$command]);
    }
}
