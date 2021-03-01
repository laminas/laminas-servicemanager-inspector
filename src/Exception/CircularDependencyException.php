<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ServiceManager\Inspector\Exception;

use Laminas\ServiceManager\Inspector\Issue\CircularDependencyIssue;
use LogicException;
use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;
use Throwable;

use function implode;

final class CircularDependencyException extends LogicException implements ExceptionInterface, IssuableInterface
{
    /**
     * @param string $name
     * @param array $instantiationStack
     * @param Throwable|null $previous
     */
    public function __construct(string $name, array $instantiationStack, ?Throwable $previous = null)
    {
        $this->instantiationStack = $instantiationStack;

        $message = sprintf(
            'Circular dependency detected: %s -> %s',
            implode(' -> ', $instantiationStack),
            $name
        );

        parent::__construct($message, 0, $previous);
    }

    public function toIssue(CodeLocation $codeLocation): PluginIssue
    {
        return new CircularDependencyIssue($this->message, $this->instantiationStack, $codeLocation);
    }
}
