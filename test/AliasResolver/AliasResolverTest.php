<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\AliasResolver;

use Laminas\ServiceManager\Inspector\AliasResolver\AliasResolver;
use Laminas\ServiceManager\Inspector\Exception\CyclicAliasException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\AliasResolver\AliasResolver
 */
class AliasResolverTest extends TestCase
{
    public function testEmptyResolvedAliasesOnEmptyProvidedAliases()
    {
        $resolvedAliases = (new AliasResolver())([]);

        $this->assertSame([], $resolvedAliases);
    }

    public function testAliasChainResolvesCorrectly()
    {
        $aliases = [
            'alias1' => 'alias2',
            'alias2' => 'alias3',
        ];

        $resolvedAliases = (new AliasResolver())($aliases);

        $this->assertSame(['alias1' => 'alias3', 'alias2' => 'alias3'], $resolvedAliases);
    }

    public function testCyclicAliasThrowsException()
    {
        $this->expectException(CyclicAliasException::class);

        $cyclicAliases = [
            'alias1' => 'alias2',
            'alias2' => 'alias1',
        ];

        (new AliasResolver())($cyclicAliases);
    }
}