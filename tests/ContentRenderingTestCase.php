<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3\Tests;

use FFI\Headers\GLFW3;
use FFI\Headers\GLFW3\Context;
use FFI\Headers\GLFW3\Platform;
use FFI\Headers\GLFW3\Version;

final class ContentRenderingTestCase extends TestCase
{
    /**
     * @testdox Testing that the headers are successfully built
     *
     * @dataProvider configDataProvider
     */
    public function testRenderable(Platform $platform, Context $context, Version $version): void
    {
        $this->expectNotToPerformAssertions();

        (string)GLFW3::create($platform, $context, $version);
    }

    /**
     * @testdox Testing that headers contain correct syntax
     *
     * @depends testRenderable
     * @dataProvider configDataProvider
     */
    public function testCompilation(Platform $platform, Context $context, Version $version): void
    {
        $this->assertHeadersSyntaxValid(
            GLFW3::create($platform, $context, $version)
        );
    }
}
