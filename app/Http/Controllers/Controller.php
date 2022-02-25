<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

       /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSuccessResponse($result, $message)
    {
        $response = [
            'success' => true,
            'status' => 1,
            'message' => $message,
            'data'    => $result,
            
        ];

        return response()->json($response, 200);
    }

    protected function sendBadRequestResponse($errors)
    {
        return response()->json([
            "message" => "Invalid user request",
            "errors" => $errors,
            "status" => 0,
            "status_code" => 400,
        ], 400);
    }

}
