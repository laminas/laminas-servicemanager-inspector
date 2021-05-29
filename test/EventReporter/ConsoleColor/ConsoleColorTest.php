<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\EventReporter\ConsoleColor;

use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\ConsoleColor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\ConsoleColor
 */
class ConsoleColorTest extends TestCase
{
    public function testSuccessWrapsStringInGreenColor()
    {
        $color = new ConsoleColor();
        $colored = $color->success('a');

        $this->assertSame("\033[32ma\033[39m", $colored);
    }

    public function testNormalWrapsStringInWhiteColor()
    {
        $color = new ConsoleColor();
        $colored = $color->normal('a');

        $this->assertSame("\033[37ma\033[39m", $colored);
    }

    public function testWarningWrapsStringInYellownColor()
    {
        $color = new ConsoleColor();
        $colored = $color->warning('a');

        $this->assertSame("\033[33ma\033[39m", $colored);
    }

    public function testErrorWrapsStringInREdColor()
    {
        $color = new ConsoleColor();
        $colored = $color->error('a');

        $this->assertSame("\033[31ma\033[39m", $colored);
    }

    public function testCriticalWrapsStringInWhiteColorWithRedBackground()
    {
        $color = new ConsoleColor();
        $colored = $color->critical('a');

        $this->assertSame("\033[37;41ma\033[39;49m", $colored);
    }
}