<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Exception;

use Laminas\PsalmPlugin\Issue\MissingFactoryIssue;
use LogicException;
use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;
use Throwable;

use function sprintf;

final class MissingFactoryException extends LogicException implements ExceptionInterface, IssuableInterface
{
    /**
     * @var
     */
    private $name;

    /**
     * @param string $name
     * @param Throwable|null $previous
     */
    public function __construct(string $name, Throwable $previous = null)
    {
        $this->name = $name;

        parent::__construct(sprintf("No factory is provided for '%s' service.", $name), 0, $previous);
    }

    public function toIssue(CodeLocation $codeLocation): PluginIssue
    {
        return new MissingFactoryIssue($this->name, $codeLocation);
    }
}
