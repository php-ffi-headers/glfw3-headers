<?php

/**
 * This file is part of GLFW3 Headers package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3\Tests;

use FFI\Env\Runtime;
use FFI\Headers\GLFW3;
use FFI\Headers\GLFW3\ContextPlatform;
use FFI\Headers\GLFW3\Version;
use FFI\Headers\GLFW3\WindowPlatform;
use FFI\Location\Locator;

/**
 * @group binary-compatibility
 * @requires extension ffi
 */
class BinaryCompatibilityTestCase extends TestCase
{
    private const WIN32_ARCHIVE_DIRECTORY = __DIR__ . '/storage/glfw3.win64.zip';
    private const WIN32_BINARY = __DIR__ . '/storage/glfw3.dll';
    private const DARWIN_ARCHIVE_DIRECTORY = __DIR__ . '/storage/glfw3.darwin.zip';
    private const DARWIN_BINARY = __DIR__ . '/storage/libglfw.3.dylib';

    public function setUp(): void
    {
        if (!Runtime::isAvailable()) {
            $this->markTestSkipped('An ext-ffi extension must be available and enabled');
        }

        parent::setUp();
    }

    protected function getWindowsBinary(): string
    {
        $version = Version::LATEST->toString();

        // Download glfw archive
        if (!\is_file(self::WIN32_ARCHIVE_DIRECTORY)) {
            $url = \vsprintf('https://github.com/glfw/glfw/releases/download/%s/glfw-%1$s.bin.WIN64.zip', [
                $version
            ]);

            \stream_copy_to_stream(\fopen($url, 'rb'), \fopen(self::WIN32_ARCHIVE_DIRECTORY, 'ab+'));
        }

        if (!\is_file(self::WIN32_BINARY)) {
            $directory = \dirname(self::WIN32_ARCHIVE_DIRECTORY);
            $file = \sprintf('glfw-%1$s.bin.WIN64/lib-vc2022/glfw3.dll', $version);
            $pathname = $directory . '/' . $file;

            if (!\is_file($pathname)) {
                $phar = new \PharData(self::WIN32_ARCHIVE_DIRECTORY);
                $phar->extractTo($directory, $file);
            }

            \rename($pathname, self::WIN32_BINARY);
        }

        return self::WIN32_BINARY;
    }

    protected function getLinuxBinary(): string
    {
        $binary = Locator::resolve('libglfw.so.3', 'libglfw.so');

        if ($binary === null) {
            $this->markTestSkipped('The [libglfw] library must be installed');
        }

        return (string)$binary;
    }

    protected function getDarwinBinary(): string
    {
        if ($library = Locator::resolve('glfw3.dylib', 'glfw.dylib', 'libglfw.3.dylib', 'libglfw.dylib')) {
            return $library;
        }

        $version = Version::LATEST->toString();

        // Download glfw archive
        if (!\is_file(self::DARWIN_ARCHIVE_DIRECTORY)) {
            $url = \vsprintf('https://github.com/glfw/glfw/releases/download/%s/glfw-%1$s.bin.MACOS.zip', [
                $version
            ]);

            \stream_copy_to_stream(\fopen($url, 'rb'), \fopen(self::DARWIN_ARCHIVE_DIRECTORY, 'ab+'));
        }

        if (!\is_file(self::DARWIN_BINARY)) {
            $directory = \dirname(self::DARWIN_ARCHIVE_DIRECTORY);
            $file = \sprintf('glfw-%1$s.bin.MACOS/lib-x86_64/libglfw.3.dylib', $version);
            $pathname = $directory . '/' . $file;

            if (!\is_file($pathname)) {
                $phar = new \PharData(self::DARWIN_ARCHIVE_DIRECTORY);
                $phar->extractTo($directory, $file);
            }

            \rename($pathname, self::DARWIN_BINARY);
        }

        return self::DARWIN_BINARY;
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

    protected function assertVersionCompare(Version $version, string $binary): void
    {
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
     * @requires extension phar
     *
     * @dataProvider versionsDataProvider
     */
    public function testWin32PlatformWithoutContext(Version $version): void
    {
        $this->expectNotToPerformAssertions();

        $binary = $this->getWindowsBinary();

        $this->assertVersionCompare($version, $binary);
        \FFI::cdef((string)GLFW3::create(WindowPlatform::WIN32, null, $version), $binary);
    }

    /**
     * @requires OSFAMILY Linux
     * @dataProvider versionsDataProvider
     */
    public function testX11PlatformWithoutContext(Version $version): void
    {
        $this->expectNotToPerformAssertions();

        $binary = $this->getLinuxBinary();

        $this->assertVersionCompare($version, $binary);
        \FFI::cdef((string)GLFW3::create(WindowPlatform::X11, null, $version), $binary);
    }

    /**
     * @requires OSFAMILY Linux
     * @dataProvider versionsDataProvider
     */
    public function testWaylandPlatformWithoutContext(Version $version): void
    {
        if (!isset($_SERVER['XDG_SESSION_TYPE']) || $_SERVER['XDG_SESSION_TYPE'] !== 'wayland') {
            $this->markTestSkipped('The Wayland window system server required');
        }

        $this->expectNotToPerformAssertions();

        $binary = $this->getLinuxBinary();

        $this->assertVersionCompare($version, $binary);
        \FFI::cdef((string)GLFW3::create(WindowPlatform::WAYLAND, null, $version), $binary);
    }

    /**
     * @requires OSFAMILY Darwin
     * @dataProvider versionsDataProvider
     */
    public function testCocoaPlatformWithoutContext(Version $version): void
    {
        $this->expectNotToPerformAssertions();

        $binary = $this->getDarwinBinary();

        $this->assertVersionCompare($version, $binary);
        \FFI::cdef((string)GLFW3::create(WindowPlatform::COCOA, null, $version), $binary);
    }

    /**
     * @dataProvider configDataProvider
     */
    public function testCompilation(WindowPlatform $window, ContextPlatform $context, Version $version): void
    {
        $this->expectException(\FFI\Exception::class);
        $this->expectExceptionMessage('Failed resolving C function');

        \FFI::cdef((string)GLFW3::create($window, $context, $version));
    }
}
