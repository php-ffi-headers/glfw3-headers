<p align="center">
    <a href="https://github.com/php-ffi-libs">
        <img src="https://avatars.githubusercontent.com/u/101121010?s=256" width="128" />
    </a>
</p>

<p align="center">
    <a href="https://github.com/php-ffi-libs/glfw3-headers/actions"><img src="https://github.com/php-ffi-libs/glfw3-headers/workflows/build/badge.svg"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://img.shields.io/badge/PHP-8.1.0-ff0140.svg"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://img.shields.io/badge/GLFW3-3.3.6-cc3c20.svg"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://poser.pugx.org/ffi-libs/glfw3-headers/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://poser.pugx.org/ffi-libs/glfw3-headers/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://packagist.org/packages/ffi-libs/glfw3-headers"><img src="https://poser.pugx.org/ffi-libs/glfw3-headers/downloads" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/php-ffi-libs/glfw3-headers/master/LICENSE.md"><img src="https://poser.pugx.org/ffi-libs/glfw3-headers/license" alt="License MIT"></a>
</p>

# GLFW3 Headers

This is a C headers of the [GLFW3](https://www.glfw.org/) adopted for PHP.

## Requirements

- PHP >= 8.1

## Installation

Library is available as composer repository and can be installed using the
following command in a root of your project.

```sh
$ composer require ffi-libs/glfw3-headers
```

## Usage

```php
use FFI\Headers\GLFW3;

$headers = GLFW3::create(
    GLFW3\WindowPlatform::X11,  // Window API
    GLFW3\ContextPlatform::GLX, // Context API
    GLFW3\Version::V3_3_6,      // GLFW Headers Version
);

echo $headers;
```

> Please note that the use of header files is not the latest version:
> - Takes time to download and install (This will be done in the background 
>   during initialization).
> - May not be compatible with the PHP headers library.

