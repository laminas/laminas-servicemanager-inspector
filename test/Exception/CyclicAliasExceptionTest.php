<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Exception;

use Laminas\ServiceManager\Inspector\Exception\CyclicAliasException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Exception\CyclicAliasException
 */
class CyclicAliasExceptionTest extends TestCase
{
    public function testReturnsProperMessage()
    {
        $e = new CyclicAliasException();

        $this->assertSame('A cycle was detected was detected within provided aliases', $e->getMessage());
    }
}