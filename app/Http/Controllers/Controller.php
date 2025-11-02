<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function responseSuccess($data)
    {
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function responseFailure($message)
    {
        return response()->json(['success' => false, 'message' => $message]);
    }
}
