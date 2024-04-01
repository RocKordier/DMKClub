<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\PDF\Generator;

class GeneratorNotFoundException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?? 'PDF Generator not found.', $code, $previous);
    }
}
