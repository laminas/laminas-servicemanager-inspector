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

/**
 * Actually it's never reached - ServiceManager would throw
 * an exception upon initialization in case of cyclic alias.
 * It might be useful later when we switch to raw config.php analysis.
 */
final class CyclicAliasException extends LogicException implements ExceptionInterface
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('A cycle was detected was detected within provided aliases', 0, $previous);
    }
}
