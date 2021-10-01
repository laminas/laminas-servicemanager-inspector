<?php

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
