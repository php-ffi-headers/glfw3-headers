<?php

/**
 * This file is part of GLFW3 Headers package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3\Tests;

use FFI\Contracts\Preprocessor\Exception\DirectiveDefinitionExceptionInterface;
use FFI\Headers\GLFW3;
use FFI\Headers\GLFW3\ContextPlatform;
use FFI\Headers\GLFW3\Version;
use FFI\Headers\GLFW3\WindowPlatform;
use FFI\Preprocessor\Preprocessor;

class ContentRenderingTestCase extends TestCase
{
    /**
     * @var Preprocessor
     */
    private static Preprocessor $pre;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$pre = new Preprocessor();

        parent::setUpBeforeClass();
    }

    /**
     * @return array{WindowPlatform, ContextPlatform, Version}
     */
    public function configDataProvider(): array
    {
        $result = [];

        foreach (WindowPlatform::cases() as $window) {
            foreach (ContextPlatform::cases() as $context) {
                foreach (Version::cases() as $version) {
                    $name = \sprintf('%s-%s-%s', $window->name, $context->name, $version->value);

                    $result[$name] = [$window, $context, $version];
                }
            }
        }

        return $result;
    }

    /**
     * @testdox Testing that the headers are successfully collected with different parameters.
     *
     * @dataProvider configDataProvider
     * @throws DirectiveDefinitionExceptionInterface
     */
    public function testRenderable(WindowPlatform $platform, ContextPlatform $context, Version $version): void
    {
        $this->expectNotToPerformAssertions();

        (string)GLFW3::create($platform, $context, $version, self::$pre);
    }
}
