<?php

/**
 * This file is part of GLFW3 Headers package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers;

use FFI\Contracts\Headers\HeaderInterface;
use FFI\Contracts\Preprocessor\Exception\DirectiveDefinitionExceptionInterface;
use FFI\Contracts\Preprocessor\Exception\PreprocessorExceptionInterface;
use FFI\Contracts\Preprocessor\PreprocessorInterface;
use FFI\Headers\GLFW3\ContextPlatform;
use FFI\Headers\GLFW3\Version;
use FFI\Headers\GLFW3\VersionInterface;
use FFI\Headers\GLFW3\WindowPlatform;
use FFI\Preprocessor\Preprocessor;

class GLFW3 implements HeaderInterface
{
    /**
     * @var non-empty-string
     */
    private const HEADERS_DIRECTORY = __DIR__ . '/../resources/headers';

    /**
     * @param PreprocessorInterface $pre
     * @param VersionInterface $version
     */
    public function __construct(
        public readonly PreprocessorInterface $pre,
        public readonly VersionInterface $version = Version::LATEST,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getHeaderPathname(): string
    {
        return self::HEADERS_DIRECTORY . '/' . $this->version->toString() . '/glfw3.h';
    }

    /**
     * @return non-empty-string
     */
    public function getNativeHeaderPathname(): string
    {
        return self::HEADERS_DIRECTORY . '/' . $this->version->toString() . '/glfw3native.h';
    }

    /**
     * @param WindowPlatform|null $window
     * @param ContextPlatform|null $context
     * @param VersionInterface|non-empty-string $version
     * @param PreprocessorInterface $pre
     * @return self
     * @throws DirectiveDefinitionExceptionInterface
     */
    public static function create(
        WindowPlatform $window = null,
        ContextPlatform $context = null,
        VersionInterface|string $version = Version::LATEST,
        PreprocessorInterface $pre = new Preprocessor(),
    ): self {
        $pre = clone $pre;

        $pre->define('GLFWAPI');
        $pre->add('stdint.h', '');
        $pre->add('stddef.h', '');
        $pre->add('GL/gl.h', '');

        // Add Windows typedefs
        if (\PHP_OS_FAMILY === 'Windows') {
            $pre->define('_WIN32', '1');
            $pre->define('WINGDIAPI');
            $pre->define('CALLBACK');
            $pre->add('windows.h', <<<'CPP'
                typedef void* HWND;
            CPP);
        }

        // Expose Window API
        $pre->define(match ($window) {
            WindowPlatform::WIN32   => 'GLFW_EXPOSE_NATIVE_WIN32',
            WindowPlatform::COCOA   => 'GLFW_EXPOSE_NATIVE_COCOA',
            WindowPlatform::X11     => 'GLFW_EXPOSE_NATIVE_X11',
            WindowPlatform::WAYLAND => 'GLFW_EXPOSE_NATIVE_WAYLAND',
            default => 'GLFW_EXPOSE_NATIVE_UNKNOWN_WINDOW_API'
        }, '1');

        // Expose Context API
        $pre->define(match ($context) {
            ContextPlatform::WGL    => 'GLFW_EXPOSE_NATIVE_WGL',
            ContextPlatform::NSGL   => 'GLFW_EXPOSE_NATIVE_NSGL',
            ContextPlatform::GLX    => 'GLFW_EXPOSE_NATIVE_GLX',
            ContextPlatform::EGL    => 'GLFW_EXPOSE_NATIVE_EGL',
            ContextPlatform::OSMESA => 'GLFW_EXPOSE_NATIVE_OSMESA',
            default => 'GLFW_EXPOSE_NATIVE_UNKNOWN_CONTEXT_API'
        }, '1');

        if (! $version instanceof VersionInterface) {
            $version = Version::create($version);
        }

        return new self($pre, $version);
    }

    /**
     * @return non-empty-string
     * @throws PreprocessorExceptionInterface
     */
    public function __toString(): string
    {
        return \implode(\PHP_EOL, [
            $this->pre->process(new \SplFileInfo($this->getHeaderPathname())),
            $this->pre->process(new \SplFileInfo($this->getNativeHeaderPathname()))
        ]) . \PHP_EOL;
    }
}
