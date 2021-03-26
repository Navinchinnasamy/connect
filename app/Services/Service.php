<?php

namespace App\Services;

use Log;
use Laravel\Lumen\Routing\Controller as BaseController;

class Service extends BaseController
{
    protected function showResponse($data, $code = 200)
    {
        $response = [
            'code' => 200,
            'status' => "success",
            'data' => $data
        ];

        return response()->json($response, $response['code']);
    }

    protected function showErrorResponse($data)
    {
        $response = [
            'code' => 500,
            'status' => "error",
            'data' => $data
        ];

        return response()->json($response, $response['code']);
    }

    protected function showValidationResponse($data)
    {
        $response = [
            'code' => 422,
            'status' => "failed",
            'data' => $data
        ];

        return response()->json($response, $response['code']);
    }

    protected function showOtherResponse($message, $code = 404)
    {
        $response = [
            'code' => $code,
            'status' => "error",
            'message' => $message
        ];

        return response()->json($response, $response['code']);
    }

    protected function showListResponse($data)
    {
        $response = [
            'code' => 200,
            'status' => "success",
            'data' => $data
        ];

        return response()->json($response, $response['code']);
    }

    public function validator(array $request, array $rules, $messages = [])
    {
        $validator = \Validator::make($request, $rules, $messages);
        
        if ($validator->fails()) {
            $messages = $validator->messages();
            $messagesFormat = [];

            foreach ($messages->toArray() as $key => $message) {
               $messagesFormat["errors"][] = ["attribute" => $key, "message" => $message[0]];
            }

            Log::info("Validation Failed : " . json_encode($messagesFormat));

            return $this->showValidationResponse($messagesFormat);
        }

        return false;
    }
}
