<?php

declare(strict_types=1);

namespace A89s\GooeyToast\Facades;

use Illuminate\Support\Facades\Facade;
use A89s\GooeyToast\Toast as ToastInstance;

/**
 * @method static ToastInstance make(?string $title = null, ?string $type = null)
 * @method static ToastInstance success(string $title, ?string $message = null)
 * @method static ToastInstance error(string $title, ?string $message = null)
 * @method static ToastInstance warning(string $title, ?string $message = null)
 * @method static ToastInstance info(string $title, ?string $message = null)
 *
 * @see \A89s\GooeyToast\Toast
 */
class GooeyToast extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ToastInstance::class;
    }
}
