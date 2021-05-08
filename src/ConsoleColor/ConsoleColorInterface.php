<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\ConsoleColor;

interface ConsoleColorInterface
{
    public function success(string $text): string;
    public function normal(string $text): string;
    public function warning(string $text): string;
    public function error(string $text): string;
    public function critical(string $text): string;
}
