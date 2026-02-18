<?php

declare(strict_types=1);

namespace Gooey\Toast\Facades;

use Illuminate\Support\Facades\Facade;
use Gooey\Toast\Toast as ToastInstance;

/**
 * @method static ToastInstance make(?string $title = null, ?string $type = null)
 * @method static ToastInstance success(string $title, ?string $message = null)
 * @method static ToastInstance error(string $title, ?string $message = null)
 * @method static ToastInstance warning(string $title, ?string $message = null)
 * @method static ToastInstance info(string $title, ?string $message = null)
 *
 * @see \Gooey\Toast\Toast
 */
class GooeyToast extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ToastInstance::class;
    }
}
