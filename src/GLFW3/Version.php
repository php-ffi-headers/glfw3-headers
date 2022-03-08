<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3;

enum Version: string implements VersionInterface
{
    case V3_0_0 = '3.0.0';
    case V3_0_1 = '3.0.1';
    case V3_0_2 = '3.0.2';
    case V3_0_3 = '3.0.3';
    case V3_0_4 = '3.0.4';
    case V3_1_0 = '3.1.0';
    case V3_1_1 = '3.1.1';
    case V3_1_2 = '3.1.2';
    case V3_2_0 = '3.2.0';
    case V3_2_1 = '3.2.1';
    case V3_3_0 = '3.3.0';
    case V3_3_1 = '3.3.1';
    case V3_3_2 = '3.3.2';
    case V3_3_3 = '3.3.3';
    case V3_3_4 = '3.3.4';
    case V3_3_5 = '3.3.5';
    case V3_3_6 = '3.3.6';

    public const LATEST = self::V3_3_6;
    public const V3_3 = self::V3_3_6;
    public const V3_2 = self::V3_2_1;
    public const V3_1 = self::V3_1_2;
    public const V3_0 = self::V3_0_4;

    /**
     * @param non-empty-string $version
     * @return VersionInterface
     */
    public static function create(string $version): VersionInterface
    {
        /** @var array<non-empty-string, VersionInterface> $versions */
        static $versions = [];

        return self::tryFrom($version)
            ?? $versions[$version]
            ??= new class($version) implements VersionInterface {
                /**
                 * @param non-empty-string $version
                 */
                public function __construct(
                    private string $version,
                ) {
                }

                /**
                 * {@inheritDoc}
                 */
                public function toString(): string
                {
                    return $this->version;
                }
            };
    }

    /**
     * {@inheritDoc}
     */
    public function toString(): string
    {
        return $this->value;
    }
}
