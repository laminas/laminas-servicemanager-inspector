<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Exception;

use LogicException;
use Throwable;

use function sprintf;

final class MissingFactoryException extends LogicException implements ExceptionInterface
{
    public function __construct(string $name, ?Throwable $previous = null)
    {
        parent::__construct(sprintf("No factory is provided for '%s' service.", $name), 0, $previous);
    }
}
