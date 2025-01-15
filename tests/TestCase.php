<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    public function authenticate(Authenticatable|HasApiTokens $user): static
    {
        Passport::actingAs($user);

        return $this;
    }
}
