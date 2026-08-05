<?php

declare(strict_types=1);

namespace App\Domains\CMS\Enums;

enum RedirectStatusCode: int
{
    case MovedPermanently = 301;
    case Found = 302;
    case SeeOther = 303;
    case TemporaryRedirect = 307;
    case PermanentRedirect = 308;

    public function label(): string
    {
        return match ($this) {
            self::MovedPermanently => '301 Moved Permanently',
            self::Found => '302 Found',
            self::SeeOther => '303 See Other',
            self::TemporaryRedirect => '307 Temporary Redirect',
            self::PermanentRedirect => '308 Permanent Redirect',
        };
    }
}
