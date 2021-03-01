<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Issue;

use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;
use Throwable;

use function implode;
use function sprintf;

final class CircularDependencyIssue extends PluginIssue
{
    /** @var array */
    private array $instantiationStack;

    /**
     * @param array $instantiationStack
     * @param Throwable|null $previous
     */
    public function __construct(string $name, array $instantiationStack, CodeLocation $codeLocation)
    {
        $this->instantiationStack = $instantiationStack;

        $message = sprintf(
            'Circular dependency detected: %s -> %s',
            implode(' -> ', $instantiationStack),
            $name
        );

        parent::__construct($message, $codeLocation);
    }

    /**
     * @return array
     */
    public function getInstantiationStack(): array
    {
        return $this->instantiationStack;
    }
}
