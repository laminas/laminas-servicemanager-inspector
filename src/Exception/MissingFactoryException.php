<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Exception;

use Laminas\ServiceManager\Inspector\Issue\MissingFactoryIssue;
use LogicException;
use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;
use Throwable;

use function sprintf;

final class MissingFactoryException extends LogicException implements ExceptionInterface, IssuableInterface
{
    /** @var string */
    private $name;

    public function __construct(string $name, ?Throwable $previous = null)
    {
        $this->name = $name;

        parent::__construct(sprintf("No factory is provided for '%s' service.", $name), 0, $previous);
    }

    public function toIssue(CodeLocation $codeLocation): PluginIssue
    {
        return new MissingFactoryIssue($this->name, $codeLocation);
    }
}
