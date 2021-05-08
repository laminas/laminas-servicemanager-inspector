<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\Inspector\EventCollector\EventCollectorInterface;
use Zakirullin\Mess\Mess;

final class LaminasDependecyConfigFactory implements FactoryInterface
{
    /**
     * @param string             $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): DependencyConfig
    {
        $config       = $container->get('config');
        $dependencies = (new Mess($config))['service_manager']->getArrayOfStringToMixed();

        return new DependencyConfig($dependencies);
    }
}
