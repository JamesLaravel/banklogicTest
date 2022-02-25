<?php

namespace App\Services;

use App\Interfaces\IAccount;
use App\Interfaces\ITransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountService
{
    protected $account, $transaction;

    public function __construct(IAccount $account, ITransaction $transaction)
    {
        $this->account = $account;
        $this->transaction = $transaction;
    }

    public function open($request)
    {
        try {
            //code...
            $account_number = $this->generateAccountNumber();
           
            $create = $this->account->insert([
                'account_no'=> $account_number,
                'bvn'=> $request->bvn,
                'first_name'=> $request->first_name,
                'last_name'=> $request->last_name,
                'email'=> $request->email,
                'location'=> $request->address,
                'maiden_name'=> $request->maiden_name,
                'balance'=> $request->deposit,
                'created_at'=> Carbon::now()
            ]);
            //$create = $this->account->insert([...$request, 'account_no'=> $account_number]);
            $new_account = $this->account->findItem(['id'=> $create], ['first_name', 'last_name', 'account_no','email', 'location','balance', 'created_at']);
            return (object)[
                'error'=> false,
                'message'=>'Account opened successfully',
                'data'=> $new_account
            ];
        } catch (\Exception $e) {
            //throw $th;
            
            return (object)[
                'error'=> true,
                'message'=> $e->getMessage()
            ];
        }
    }

    public function transfer($request)
    {
        //code...
        $amount = $request->amount;
        $sender_account = $this->account->findItem(['account_no'=> $request->account_no], ['id','balance']);
        $receiver_account = $this->account->findItem(['account_no'=> $request->receiver_no], ['id', 'balance']);
        if(is_null($sender_account)){
            return (object)[
                'error'=> true,
                'message'=> 'Invalid Account number'
            ];
        }

        if(!$receiver_account){
            return (object)[
                'error'=> true,
                'message'=> 'Invalid Receiver Account number'
            ];
        }

        // check if available can send money
        if($amount > $sender_account->balance){
            return (object)[
                'error'=> true,
                'message'=> 'Insufficient Fund'
            ];
        }
        $sender_new_balance = intval($sender_account->balance) - intval($amount);
        $receiver_new_balance = intval($amount) + intval($receiver_account->balance); 
        DB::beginTransaction();
        try {
            // remove money first from sender
            $this->account->updateItem(['id'=>$sender_account->id], ['balance'=> $sender_new_balance]);
            $this->transaction->insert([
                'tranId'=> (string) Str::orderedUuid(),
                'tran_type'=> 'debit',
                'senderId'=> $sender_account->id,
                'receiverId'=> $receiver_account->id,
                'status'=> $request->status
            ]);
            $this->account->updateItem(['id'=> $receiver_account->id], ['balance'=> $receiver_new_balance]);
            $this->transaction->insert([
                'tranId'=> (string) Str::orderedUuid(),
                'tran_type'=> 'credit',
                'senderId'=> $sender_account->id,
                'receiverId'=> $receiver_account->id,
                'status'=> $request->status
            ]);
            DB::commit();
            return (object)[
                'error'=> false,
                'message'=> 'Transfer successfull',
                'data'=> null
            ];
        } catch (\Exception $e) {
            DB::rollback();
            //throw $th;
            return (object)[
                'error'=> true,
                'message'=> $e->getMessage()
            ];
        }
    }

    public function accountDetails($account_no)
    {
        try {
            //code...
            $account = $this->account->findItem(['account_no'=> $account_no],['first_name', 'last_name', 'balance', 'email','location','maiden_name','account_no', 'bvn', 'created_at']);
            if(is_null($account)){
                return (object)[
                    'error'=> true,
                    'message'=> 'Invalid account number'
                ];
            }

            return (object)[
                'error'=> false,
                'data'=> $account
            ];
        } catch (\Exception $e) {
            //throw $th;
            return (object)[
                'error'=> true,
                'message'=> $e->getMessage()
            ];
        }
    }

    public function transferLogs($account_no)
    {
        try {
            //code...
            $account = $this->account->findItem(['account_no'=> $account_no], ['id']);
            if(is_null($account)){
                return (object)[
                    'error'=> true,
                    'message'=> 'Invalid account number'
                ];
            }
            $logs = DB::table('transactions as t')->join('accounts as a', function($join){
                $join->on('t.senderId', '=', 'a.id')->orOn('t.receiverId', '=', 'a.id');
                //$join->on('t.receiverId', '=', 'a.id');
            })->where('t.senderId', $account->id)->select(['t.tranId', 't.status', 't.tran_type', 't.created_at', 'a.first_name', 'a.last_name', 'a.account_no'])->get();

            return (object)[
                'error'=> false,
                'data'=> $logs
            ];
        } catch (\Exception $e) {
            //throw $th;
            dd($e);
            return (object)[
                'error'=> true,
                'message'=> $e->getMessage()
            ];
        }
    }

    private function generateAccountNumber()
    {
       
        do{
            $code = random_int(1000000000, 9999999999);
        }while($this->account->findItem(['account_no'=> $code],['account_no']));

        return $code;
    }
}