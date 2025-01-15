<?php

namespace App\Enums;

enum AnimalGender: string
{
    case Male = 'male';
    case Female = 'female';

    public function humanFormat(): string
    {
        return __($this->name);
    }
}
