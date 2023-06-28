<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class OauthException extends Exception
{
    public function render(Request $request): Response
    {
        return response()->view('errors.oauth_error', [
            'message' => $this->getMessage()
        ], 500);
    }
}
