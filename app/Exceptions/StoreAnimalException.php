<?php

namespace App\Exceptions;

use Exception;

class StoreAnimalException extends Exception
{
    public static function missingShelter(): self
    {
        return new self('Unable to save an animal without a shelter.');
    }
}
