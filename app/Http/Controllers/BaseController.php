<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($result, $code = 200)
    {
        $response =  [
            'success' => true,
            'message' => "success",
            'data' => $result
        ];
        // return $response;
        // return $result->additional($response);
        return response()->json($response, $code);
    }
    public function sendError( $errorMessages = [] , $code = 404)
    {
        $response =  [
            'success' => false,
            'message' => "fail"
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

    public function sendResponseFromResult($result)
    {
        if($result['success']) {
            return $this->sendResponse($result['data'], $result['code'] ?? 200);
        } else {
            return $this->sendError($result['message'], $result['code'] ?? 400);
        }
    }
}
