<?php
namespace Tests\Feature;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->authUser();
    }

    public function test_user_can_create_create_account()
    {
        $account = Account::factory()->raw();

        $this->postJson(route('account.opening'), $account)->assertCreated();
        $this->assertDatabaseHas('accounts', [
            'account_no'=> 1234567898,
            'bvn'=> $account['bvn'],
            'first_name'=> $account['first_name'],
            'last_name'=> $account['last_name'],
            'email'=> $account['email'],
            'location'=> $account['location'],
            'maiden_name'=> $account['maiden_name'],
            'balance'=> 5000,
            'created_at'=> Carbon::now()
        ]);
    }

    public function test_user_can_get_balance()
    {
        $account = $this->createAccount(['account_no'=> 1234567898]);
        $this->createAccount();

        $response = $this->getJson(route('get.balance'))->assertOk()->json('data');
        $this->assertEquals($response->data, $account->balance);
    }

   
}