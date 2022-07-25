<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\DependencyConfig;

// phpcs:ignore
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Zakirullin\Mess\Mess;

final class MezzioDependencyConfigFactory implements FactoryInterface
{
    /**
     * @param string             $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): DependencyConfig
    {
        $dependencies = (new Mess($container->get('config')))['dependencies']->getArrayOfStringToMixed();

        return new DependencyConfig($dependencies);
    }
}
