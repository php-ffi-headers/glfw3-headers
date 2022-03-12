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
use FFI\Headers\GLFW3\Version;
use FFI\Headers\GLFW3\Platform;
use FFI\Headers\Testing\Downloader;
use FFI\Location\Locator;

class BinaryCompatibilityTestCase extends TestCase
{
    protected function skipIfVersionNotCompatible(Version $version, string $binary): void
    {
        $this->skipIfNoFFISupport();

        $ffi = \FFI::cdef('void glfwGetVersion(int* major, int* minor, int* rev);', $binary);

        [$maj, $min, $rev] = [$ffi->new('int'), $ffi->new('int'), $ffi->new('int')];
        $ffi->glfwGetVersion(\FFI::addr($maj), \FFI::addr($min), \FFI::addr($rev));
        $actual = \sprintf('%d.%d.%d', $maj->cdata, $min->cdata, $rev->cdata);

        if (\version_compare($version->toString(), $actual, '>')) {
            $message = 'Unable to check compatibility because the installed version of the '
                . 'library (v%s) is lower than the tested headers (v%s)';

            $this->markTestSkipped(\sprintf($message, $actual, $version->toString()));
        }
    }

    /**
     * @requires OSFAMILY Windows
     * @dataProvider versionsDataProvider
     */
    public function testWindowsBinaryCompatibility(Version $version): void
    {
        if (!\is_file($binary = __DIR__ . '/storage/glfw.dll')) {
            $from = \sprintf('glfw-%1$s.bin.WIN64/lib-vc2022/glfw3.dll', Version::LATEST->toString());
            Downloader::zip('https://github.com/glfw/glfw/releases/download/%s/glfw-%1$s.bin.WIN64.zip', [
                Version::LATEST->toString(),
            ])
                ->extract($from, $binary);
        }

        $this->skipIfVersionNotCompatible($version, $binary);
        $this->assertHeadersCompatibleWith(GLFW3::create(Platform::WIN32, null, $version), $binary);
    }

    /**
     * @requires OSFAMILY Linux
     * @dataProvider versionsDataProvider
     */
    public function testLinuxX11BinaryCompatibility(Version $version): void
    {
        if (($binary = Locator::resolve('libglfw.so.3', 'libglfw.so')) === null) {
            $this->markTestSkipped('The [libglfw] library must be installed');
        }

        $this->skipIfVersionNotCompatible($version, $binary);
        $this->assertHeadersCompatibleWith(GLFW3::create(Platform::X11, null, $version), $binary);
    }

    /**
     * @requires OSFAMILY Linux
     * @dataProvider versionsDataProvider
     */
    public function testLinuxWaylandBinaryCompatibility(Version $version): void
    {
        if (!isset($_SERVER['XDG_SESSION_TYPE']) || $_SERVER['XDG_SESSION_TYPE'] !== 'wayland') {
            $this->markTestSkipped('The Wayland window system server required');
        }

        if (($binary = Locator::resolve('libglfw.so.3', 'libglfw.so')) === null) {
            $this->markTestSkipped('The [libglfw] library must be installed');
        }

        $this->skipIfVersionNotCompatible($version, $binary);
        $this->assertHeadersCompatibleWith(GLFW3::create(Platform::WAYLAND, null, $version), $binary);
    }

    /**
     * @requires OSFAMILY Darwin
     * @dataProvider versionsDataProvider
     */
    public function testCocoaPlatformWithoutContext(Version $version): void
    {
        $binary = Locator::resolve('glfw3.dylib', 'glfw.dylib', 'libglfw.3.dylib', 'libglfw.dylib');

        if ($binary === null) {
            $binary = __DIR__ . '/storage/glfw.dylib';

            if (!\is_file($binary)) {
                $from = \sprintf('glfw-%1$s.bin.MACOS/lib-x86_64/libglfw.3.dylib', Version::LATEST->toString());
                Downloader::zip('https://github.com/glfw/glfw/releases/download/%s/glfw-%1$s.bin.MACOS.zip', [
                    Version::LATEST->toString(),
                ])
                    ->extract($from, $binary);
            }
        }

        $this->skipIfVersionNotCompatible($version, $binary);
        $this->assertHeadersCompatibleWith(GLFW3::create(Platform::COCOA, null, $version), $binary);
    }
}
