<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum HttpStatus: int
{
    case Ok = 200;
    case Created = 201;
    case Accepted = 202;
    case NoContent = 204;

    case MovedPermanently = 301;
    case Found = 302;
    case SeeOther = 303;
    case NotModified = 304;
    case TemporaryRedirect = 307;
    case PermanentRedirect = 308;

    case BadRequest = 400;
    case Unauthorized = 401;
    case PaymentRequired = 402;
    case Forbidden = 403;
    case NotFound = 404;
    case MethodNotAllowed = 405;
    case Conflict = 409;
    case Gone = 410;
    case UnprocessableEntity = 422;
    case TooManyRequests = 429;

    case InternalServerError = 500;
    case BadGateway = 502;
    case ServiceUnavailable = 503;
    case GatewayTimeout = 504;

    public function label(): string
    {
        return match ($this) {
            self::Ok => "OK",
            self::Created => "Created",
            self::Accepted => "Accepted",
            self::NoContent => "No Content",
            self::MovedPermanently => "Moved Permanently",
            self::Found => "Found",
            self::SeeOther => "See Other",
            self::NotModified => "Not Modified",
            self::TemporaryRedirect => "Temporary Redirect",
            self::PermanentRedirect => "Permanent Redirect",
            self::BadRequest => "Bad Request",
            self::Unauthorized => "Unauthorized",
            self::PaymentRequired => "Payment Required",
            self::Forbidden => "Forbidden",
            self::NotFound => "Not Found",
            self::MethodNotAllowed => "Method Not Allowed",
            self::Conflict => "Conflict",
            self::Gone => "Gone",
            self::UnprocessableEntity => "Unprocessable Entity",
            self::TooManyRequests => "Too Many Requests",
            self::InternalServerError => "Internal Server Error",
            self::BadGateway => "Bad Gateway",
            self::ServiceUnavailable => "Service Unavailable",
            self::GatewayTimeout => "Gateway Timeout",
        };
    }

    public function isSuccess(): bool
    {
        return $this->value >= 200 && $this->value < 300;
    }

    public function isRedirect(): bool
    {
        return $this->value >= 300 && $this->value < 400;
    }

    public function isClientError(): bool
    {
        return $this->value >= 400 && $this->value < 500;
    }

    public function isServerError(): bool
    {
        return $this->value >= 500 && $this->value < 600;
    }

    public function isError(): bool
    {
        return $this->isClientError() || $this->isServerError();
    }
}
