<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\ConsoleColor;

final class NullConsoleColor implements ConsoleColorInterface
{
    public function success(string $text): string
    {
        return $text;
    }

    public function normal(string $text): string
    {
        return $text;
    }

    public function warning(string $text): string
    {
        return $text;
    }

    public function error(string $text): string
    {
        return $text;
    }

    public function critical(string $text): string
    {
        return $text;
    }
}
