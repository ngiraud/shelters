<?php

namespace App\Exceptions;

use Exception;

class StoreShelterException extends Exception
{
    public static function missingShelter(): self
    {
        return new self('Unable to save a shelter.');
    }
}
