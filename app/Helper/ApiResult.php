<?php

namespace App\Helper;

class ApiResult
{
    public static function Response($code, $message, $data)
    {
        return response()->json([
            "status" => $code,
            "message" => $message,
            "data" => $data
        ], $code);
    }
}
