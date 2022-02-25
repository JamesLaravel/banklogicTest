<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    protected $accountservice;
    public function __construct(AccountService $accountService)
    {
        $this->accountservice = $accountService;
    }

    public function openAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'=> 'required|string',
            'last_name'=> 'required|string',
            'email'=> 'required|string|email',
            'phone'=>'required|numeric',
            'bvn'=> 'required|string',
            'maiden_name'=> 'required|string',
            'deposit'=> 'required|numeric',
            'address'=> 'required|string'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse(($validator->errors()));
        }

        $open = $this->accountservice->open((object)$request->only(['first_name', 'last_name', 'email','phone', 'bvn', 'maiden_name', 'deposit', 'address']));

        if($open->error){
            return $this->sendBadRequestResponse($open->message);
        }

        return $this->sendSuccessResponse($open->data, $open->message);

    }

    public function makeTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_no'=> 'required|string',
            'amount'=> 'required|numeric',
            'receiver_no'=> 'required|numeric'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors());
        }

        $req = $request->only(['account_no', 'amount', 'receiver_no']);
        $req['status'] = 'completed';
        
        $transfer = $this->accountservice->transfer((object)$req);
        if($transfer->error){
            return $this->sendBadRequestResponse($transfer->message);
        }

        return $this->sendSuccessResponse($transfer->data, $transfer);
       

    }

    public function getBalance($account_no)
    {
        $account = $this->accountservice->accountDetails($account_no);
        if($account->error){
            return $this->sendBadRequestResponse($account->message);
        }
        return $this->sendSuccessResponse($account->data->balance, 'User Available balance');
    }

    public function transactions($account_no)
    {
        $history = $this->accountservice->transferLogs($account_no);
        if($history->error){
            return $this->sendBadRequestResponse($history->message);
        }
        return $this->sendSuccessResponse($history->data, 'Account Statements');
    }
}