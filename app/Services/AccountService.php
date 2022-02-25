<?php

namespace App\Services;

use App\Interfaces\IAccount;
use App\Interfaces\ITransaction;
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
            $create = $this->account->insert([...$request, 'account_no'=> $account_number]);
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
        if(!$sender_account){
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
        } catch (\Exception $e) {
            DB::rollback();
            //throw $th;
            return (object)[
                'error'=> true,
                'message'=> $e->getMessage()
            ];
        }
    }

    private function generateAccountNumber()
    {
        do{
            $code = random_int(10000000000, 9999999999);
        }while($this->account->findItem(['account_no'=> $code],[]));

        return $code;
    }
}