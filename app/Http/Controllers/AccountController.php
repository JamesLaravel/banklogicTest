<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    protected $accountService;
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
            'phone'=>'required|numeric|max:11',
            'bvn'=> 'required|string',
            'maiden_name'=> 'required|string',
            'deposit'=> 'required|numeric'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse(($validator->errors()));
        }

        $open = $this->accountService->open((object)$request->only(['first_name', 'last_name', 'email','phone', 'bvn', 'maiden_name', 'desposit']));

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
        
        $transfer = $this->accountService->transfer((object)$req);

        if($transfer->error){
            return $this->sendBadRequestResponse($transfer->message);
        }

        return $$this->sendSuccessResponse($transfer->data, $transfer);
       

    }
}