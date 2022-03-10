<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\GLFW3;

enum Context
{
    case WGL;
    case NSGL;
    case GLX;
    case EGL;
    case OSMESA;
}
