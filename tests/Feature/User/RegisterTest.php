<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_register()
    {
        $this->postJson(route('user.register'), [
            'role'=> 'employee',
            'username'=> 'Test',
            "password"=>"test24",
            "password_confirmation"=> "test24"
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', ['username'=> 'Test']);
    }
}