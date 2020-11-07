<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function checkID($id, $model)
    {
        $statusCode = 0;
        $response = [];
        if (!is_numeric($id)) {
            $response = [
                'status' => 400,
                'messages' => 'id must a number!',
                'data' => null
            ];
            $statusCode += 400;
        } else  if (!$model::find($id)) {
            $response = [
                'status' => 400,
                'messages' => 'not found any data, with that id!',
                'data' => null
            ];
            $statusCode += 400;
        }

        return [$response, $statusCode];
    }
}
