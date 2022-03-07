<?php

/**
 * This file is part of GLFW3 Headers package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3\Tests;

use FFI\Headers\GLFW3;
use FFI\Headers\GLFW3\ContextPlatform;
use FFI\Headers\GLFW3\Version;
use FFI\Headers\GLFW3\WindowPlatform;

/**
 * @group building
 */
final class ContentRenderingTestCase extends TestCase
{
    /**
     * @return array<array{WindowPlatform, ContextPlatform, Version}>
     */
    public function configDataProvider(): array
    {
        $result = [];

        foreach (WindowPlatform::cases() as $window) {
            foreach (ContextPlatform::cases() as $context) {
                foreach (Version::cases() as $version) {
                    $result[\sprintf('%s-%s-%s', $window->name, $context->name, $version->value)]
                        = [$window, $context, $version];
                }
            }
        }

        return $result;
    }

    /**
     * @testdox Testing that the headers are successfully built
     *
     * @dataProvider configDataProvider
     */
    public function testRenderable(WindowPlatform $window, ContextPlatform $context, Version $version): void
    {
        $this->expectNotToPerformAssertions();

        (string)GLFW3::create($window, $context, $version);
    }
}
