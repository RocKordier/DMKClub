<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Model;

/**
 * Email template not found.
 */
class TemplateNotFoundException extends \Exception
{
    public function __construct(string $message, int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
