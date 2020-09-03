<?php

namespace App\Helpers;


class ResponseHelper
{
    private $data = null;
    private $message = null;

    public static function response()
    {
        return new ResponseHelper();
    }

    public function data($data)
    {
        $this->data = $data;
        return $this;
    }
    public function message($message)
    {
        $this->message = $message;
        return $this;
    }
    public function send($code)
    {
        $status = 199 < $code && $code < 300;
        return response()->json([
            "success" => $status,
            "message" => $this->message,
            ($status ? "data" : "errors") => $this->data

        ], $code);
    }
}
