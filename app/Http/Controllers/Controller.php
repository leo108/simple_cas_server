<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function errorJson($msg, $code = -1, $data = [])
    {
        return new Response(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    public function successJson($data = [], $msg = '')
    {
        return new Response(['code' => 0, 'msg' => $msg, 'data' => $data]);
    }
}
