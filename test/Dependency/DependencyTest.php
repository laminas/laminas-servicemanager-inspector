<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Dependency;

use Laminas\ServiceManager\Inspector\Dependency\Dependency;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\Dependency\Dependency
 */
class DependencyTest extends TestCase
{
    public function testReturnsSameNameAsProvided()
    {
        $dependency = new Dependency('a');

        $this->assertSame('a', $dependency->getName());
    }

    public function testDependencyIsOptionalByDefault()
    {
        $dependency = new Dependency('a');

        $this->assertFalse($dependency->isOptional());
    }

    public function testReturnsSameIsOptionalAsProvided()
    {
        $dependency = new Dependency('a', true);

        $this->assertTrue($dependency->isOptional());
    }
}
