<?php

/**
 * This file is part of GLFW3 Headers package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3;

enum WindowPlatform
{
    case WIN32;
    case COCOA;
    case X11;
    case WAYLAND;
}
