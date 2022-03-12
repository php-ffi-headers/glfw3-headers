<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3\Tests;

use FFI\Headers\GLFW3\Context;
use FFI\Headers\GLFW3\Version;
use FFI\Headers\GLFW3\Platform;
use FFI\Headers\Testing\TestingTrait;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use TestingTrait;

    /**
     * @return array<array{Platform, Context, Version}>
     */
    public function configDataProvider(): array
    {
        $result = [];

        $mapping = [
            [Platform::WIN32, Context::WGL],
            [Platform::X11, Context::GLX],
            [Platform::X11, Context::EGL],
            [Platform::WAYLAND, Context::GLX],
            [Platform::WAYLAND, Context::EGL],
            [Platform::COCOA, Context::OSMESA],
            [Platform::COCOA, Context::NSGL],
        ];

        foreach ($mapping as [$window, $context]) {
            foreach (Version::cases() as $version) {
                $result[\sprintf('%s-%s-%s', $window->name, $context->name, $version->value)]
                    = [$window, $context, $version];
            }
        }

        return $result;
    }

    /**
     * @return array<array{Version}>
     */
    public function versionsDataProvider(): array
    {
        $result = [];

        foreach (Version::cases() as $version) {
            $result[$version->toString()] = [$version];
        }

        return $result;
    }
}
