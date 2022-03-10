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
use FFI\Headers\GLFW3\Version;
use FFI\Headers\GLFW3\Platform;

/**
 * @group building
 */
final class ContentRenderingTestCase extends TestCase
{
    /**
     * @testdox Testing that the headers are successfully built
     *
     * @dataProvider configDataProvider
     */
    public function testRenderable(Platform $window, Context $context, Version $version): void
    {
        $this->expectNotToPerformAssertions();

        (string)GLFW3::create($window, $context, $version);
    }
}
