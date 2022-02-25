<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;    
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role'=> 'required|string|in:employee,user',
            'bvn'=> 'required_if:role,user',
            'account_no'=> 'required_if:role,user',
            'username'=> 'required|string|unique:users',
            'password'=> 'required|alpha_num|confirmed',           
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors());
        }

        $newUser = $this->userService->add((object) $request->only(['username', 'password', 'role', 'bvn', 'account_no']));

        if($newUser->error){
            return $this->sendBadRequestResponse($newUser->message);
        }

        return $this->sendSuccessResponse($newUser->data, $newUser->message);
        
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'=> 'required|string',
            'password'=> 'required|string'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors());
        }

        $access = $this->userService->login((object)$request->only('username', 'password'));
        if($access->error){
            return $this->sendBadRequestResponse($access->message);
        }

        return $this->sendSuccessResponse($access->data, $access->message);

    }
}