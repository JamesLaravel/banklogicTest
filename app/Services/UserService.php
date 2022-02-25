<?php

namespace App\Services;

use App\Interfaces\IAccount;
use App\Interfaces\IUser;

class UserService
{
    protected $user, $account;

    public function __construct(IUser $user, IAccount $account)
    {
        $this->user = $user;
        $this->account = $account;
    }

    public function add($request)
    {
        
        try {
            //code...
            if($request->bvn){
               $check_account_details = $this->account->findItem(['account_no'=> $request->account_no, 'bvn'=> $request->bvn], ['id']);
               if(!$check_account_details){
                   return (object)[
                    'error'=> true,
                    'message'=> 'Invalid account details'
                   ];
               }
            }

            $create = $this->user->insert([...$request]);
            $newlogin = $this->user->findItem(['id'=>$create], ['username']);

            return (object)[
                'error'=> false,
                'message'=> 'User created',
                'newlogin'=> $newlogin
            ];
            
            
        } catch (\Exception $e) {
            //throw $th;
            return (object)[
                'error'=> true,
                'message'=> $e->getMessage()
            ];
        }
    }
}