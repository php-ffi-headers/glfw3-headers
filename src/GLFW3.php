<?php

/**
 * This file is part of FFI package.
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
use FFI\Headers\GLFW3\Context;
use FFI\Headers\GLFW3\HeadersDownloader;
use FFI\Headers\GLFW3\Version;
use FFI\Contracts\Headers\VersionInterface;
use FFI\Headers\GLFW3\Platform;
use FFI\Preprocessor\Preprocessor;

class GLFW3 implements HeaderInterface
{
    /**
     * @var non-empty-string
     */
    private const HEADERS_DIRECTORY = __DIR__ . '/../resources/headers';

    /**
     * @var non-empty-string
     */
    private const WINDOWS_H = <<<'CPP'
    typedef void* HWND;
    typedef void* HGLRC;
    CPP;

    /**
     * @var non-empty-string
     */
    private const APPLICATION_SERVICES_H = <<<'CPP'
    typedef uint32_t CGDirectDisplayID;
    CPP;

    /**
     * @var non-empty-string
     */
    private const XLIB_H = <<<'CPP'
    typedef unsigned long XID;
    typedef XID Window;
    typedef unsigned long VisualID;
    typedef struct _XDisplay Display;
    CPP;

    /**
     * @var non-empty-string
     */
    private const XRANDR_H = <<<'CPP'
    typedef XID RROutput;
    typedef XID RRCrtc;
    CPP;

    /**
     * @param PreprocessorInterface $pre
     * @param VersionInterface $version
     */
    public function __construct(
        public readonly PreprocessorInterface $pre,
        public readonly VersionInterface $version = Version::LATEST,
    ) {
        if (!$this->exists()) {
            HeadersDownloader::download($this->version, self::HEADERS_DIRECTORY);

            if (!$this->exists()) {
                throw new \RuntimeException('Could not initialize (download) header files');
            }
        }
    }

    /**
     * @return bool
     */
    private function exists(): bool
    {
        return \is_file($this->getHeaderPathname())
            && \is_file($this->getNativeHeaderPathname())
        ;
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
     * @param Platform|null $platform
     * @param Context|null $context
     * @param VersionInterface|non-empty-string $version
     * @param PreprocessorInterface $pre
     * @return self
     * @throws DirectiveDefinitionExceptionInterface
     */
    public static function create(
        Platform $platform = null,
        Context $context = null,
        VersionInterface|string $version = Version::LATEST,
        PreprocessorInterface $pre = new Preprocessor(),
    ): self {
        $pre = clone $pre;

        $pre->define('GLFWAPI');
        $pre->add('stdint.h', '');
        $pre->add('stddef.h', '');
        $pre->add('GL/gl.h', '');

        // Expose Window API
        switch ($platform) {
            case Platform::WIN32:
                $pre->define('GLFW_EXPOSE_NATIVE_WIN32', '1');
                $pre->define('_WIN32', '1');
                $pre->define('WINGDIAPI');
                $pre->define('CALLBACK');
                $pre->add('windows.h', self::WINDOWS_H);
                break;

            case Platform::COCOA:
                $pre->define('GLFW_EXPOSE_NATIVE_COCOA', '1');
                $pre->add('ApplicationServices/ApplicationServices.h', self::APPLICATION_SERVICES_H);
                break;

            case Platform::X11:
                $pre->define('GLFW_EXPOSE_NATIVE_X11', '1');
                $pre->add('X11/Xlib.h', self::XLIB_H);
                $pre->add('X11/extensions/Xrandr.h', self::XRANDR_H);
                break;

            case Platform::WAYLAND:
                $pre->define('GLFW_EXPOSE_NATIVE_WAYLAND', '1');
                $pre->add('wayland-client.h', '');
                break;
        }

        // Expose Context API
        switch ($context) {
            case Context::WGL:
                $pre->define('GLFW_EXPOSE_NATIVE_WGL', '1');
                $pre->add('windows.h', self::WINDOWS_H);
                break;

            case Context::NSGL:
                $pre->define('GLFW_EXPOSE_NATIVE_NSGL', '1');
                $pre->add('ApplicationServices/ApplicationServices.h', self::APPLICATION_SERVICES_H);
                break;

            case Context::GLX:
                $pre->define('GLFW_EXPOSE_NATIVE_GLX', '1');
                $pre->add('GL/glx.h', <<<'CPP'
                 #ifndef GLFW_EXPOSE_NATIVE_X11
                    typedef unsigned long XID;
                #endif
                typedef void* GLXContext;
                typedef XID GLXWindow;
                CPP);
                $pre->add('X11/Xlib.h', self::XLIB_H);
                $pre->add('X11/extensions/Xrandr.h', self::XRANDR_H);
                break;

            case Context::EGL:
                $pre->define('GLFW_EXPOSE_NATIVE_EGL', '1');
                $pre->add('EGL/egl.h', <<<'CPP'
                typedef void *EGLDisplay;
                typedef void *EGLContext;
                typedef void *EGLSurface;
                CPP);
                break;

            case Context::OSMESA:
                $pre->define('GLFW_EXPOSE_NATIVE_OSMESA', '1');
                $pre->add('GL/osmesa.h', <<<'CPP'
                typedef struct osmesa_context *OSMesaContext;
                CPP);
                break;
        }

        if (!$version instanceof VersionInterface) {
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
        $headers = [
            $this->pre->process(new \SplFileInfo($this->getHeaderPathname())),
            $this->pre->process(new \SplFileInfo($this->getNativeHeaderPathname()))
        ];

        return \implode(\PHP_EOL, $headers) . \PHP_EOL;
    }
}
