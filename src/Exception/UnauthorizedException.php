<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException implements AppExceptionInterface
{

    public function __construct(string $message)
    {

        parent::__construct(400, $message);
    }
}