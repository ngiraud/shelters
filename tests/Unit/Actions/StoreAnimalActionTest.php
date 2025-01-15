<?php

namespace Tests\Unit\Actions;

use App\Actions\Animal\StoreAnimalAction;
use App\Exceptions\StoreAnimalException;
use PHPUnit\Framework\TestCase;

class StoreAnimalActionTest extends TestCase
{
    public function test_should_throw_an_exception_if_no_shelter_provided(): void
    {
        $this->expectExceptionObject(StoreAnimalException::missingShelter());

        app(StoreAnimalAction::class)->execute(['value' => 'a']);
    }
}
