<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3\Tests;

use FFI\Headers\GLFW3\ContextPlatform;
use FFI\Headers\GLFW3\Version;
use FFI\Headers\GLFW3\WindowPlatform;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return array<array{WindowPlatform, ContextPlatform, Version}>
     */
    public function configDataProvider(): array
    {
        $result = [];

        $mapping = [
            [WindowPlatform::WIN32, ContextPlatform::WGL],
            [WindowPlatform::X11, ContextPlatform::GLX],
            [WindowPlatform::X11, ContextPlatform::EGL],
            [WindowPlatform::WAYLAND, ContextPlatform::GLX],
            [WindowPlatform::WAYLAND, ContextPlatform::EGL],
            [WindowPlatform::COCOA, ContextPlatform::OSMESA],
            [WindowPlatform::COCOA, ContextPlatform::NSGL],
        ];

        foreach ($mapping as [$window, $context]) {
            foreach (Version::cases() as $version) {
                $result[\sprintf('%s-%s-%s', $window->name, $context->name, $version->value)]
                    = [$window, $context, $version];
            }
        }

        return $result;
    }
}
