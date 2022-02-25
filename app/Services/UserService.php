<?php

namespace App\Services;

use App\Interfaces\IAccount;
use App\Interfaces\IUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
            if(isset($request->bvn)){
               $check_account_details = $this->account->findItem(['account_no'=> $request->account_no, 'bvn'=> $request->bvn], ['id']);
               if(!$check_account_details){
                   return (object)[
                    'error'=> true,
                    'message'=> 'Invalid account details'
                   ];
               }
            }

            $request->password = bcrypt($request->password);
            
            $create = $this->user->insert([
                'username'=> $request->username,
                'password'=> $request->password,
                'role'=> $request->role,
                'bvn'=> isset($request->bvn) ? $request->bvn : null
            ]);
            $newlogin = $this->user->findItem(['id'=>$create], ['username']);

            return (object)[
                'error'=> false,
                'message'=> 'User created',
                'data'=> $newlogin
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

    public function login($request)
    {
       
        //$user = $this->user->findItem(['username'=> $request->username], ['id', 'username', 'password', 'role']);
        $user = User::where('username', $request->username)->first();
        if(is_null($user)){
            return (object)[
                'error'=> true,
                'message'=> 'Invalid username or password'
            ];
        }

        if(!Hash::check($request->password, $user->password)){
            return (object)[
                'error'=> true,
                'message'=> 'Invalid username or password'
            ];
        }

        
        if($user->role === 'employees'){
            $token = $user->createToken('employee users', ['employee-users'])->accessToken;
        }

        return (object)[
            'error'=> false,
            'message'=> 'User Login successfully',
            'data'=> ['token'=> $token, 'token_type'=> 'Bearer', 'expires_at'=> Carbon::now()->addHours(2)]
        ];

        
    }
}