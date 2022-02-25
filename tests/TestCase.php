<?php

namespace Tests;

use App\Models\Account;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function createUser($args = [])
    {
        return User::factory()->create($args);
    }

    public function authUser()
    {
        $user = $this->createUser();
        Passport::actingAs($user, ['employee-users']);
        return $user;
    }

    public function createAccount($args = [])
    {
        return Account::factory()->create($args);
    }

    
}
