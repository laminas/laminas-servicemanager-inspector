<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ServiceManager\Inspector\Exception;

use Laminas\ServiceManager\Inspector\Issue\UnexpectedScalarTypeIssue;
use LogicException;
use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;
use Throwable;

use function sprintf;

// TODO issue
final class UnexpectedScalarTypeException extends LogicException implements ExceptionInterface, IssuableInterface
{
    private $serviceName;

    private $paramName;

    /**
     * @param string $serviceName
     * @param string $paramName
     * @param Throwable|null $previous
     */
    public function __construct(string $serviceName, string $paramName, Throwable $previous = null)
    {
        $this->serviceName = $serviceName;
        $this->paramName = $paramName;

        parent::__construct(
            sprintf(
                "ReflectionBasedAbstractFactory cannot resolve scalar '%s' for '%s' service.",
                $paramName,
                $serviceName
            ),
            0,
            $previous
        );
    }

    public function toIssue(CodeLocation $codeLocation): PluginIssue
    {
        return new UnexpectedScalarTypeIssue($this->serviceName, $this->paramName, $codeLocation);
    }
}
