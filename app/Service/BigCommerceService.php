<?php

namespace App\Service;

use App\Exceptions\OauthException;
use App\Http\Controllers\IndexController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BigCommerceService {
    private $baseURL;
    public $clientId;
    private $appSecret;

    public function __construct()
    {
        $this->baseURL = env('APP_URL');
        $this->clientId = env('BC_APP_CLIENT_ID');
        $this->appSecret = env('BC_APP_SECRET');
    }

    public function getAccessToken(Request $request){
        return $request->session()->get('access_token');
    }

    public function getStoreHash(Request $request)
    {
    	
        return $request->session()->get('store_hash');
    }


    public function handleInstall(Request $request)
    {
        // Make sure all required query params have been passed
        if (
            !$request->has('code') || 
            !$request->has('scope') || 
            !$request->has('context')
        ) {
            throw new OauthException("Recieved invalid data for Oauth Authentication");
        }

        try {
            

            $result = Http::acceptJson()->throw()->post('https://login.bigcommerce.com/oauth2/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->appSecret,
                'redirect_uri' => $this->baseURL . '/auth/install',
                'grant_type' => 'authorization_code',
                'code' => $request->get('code'),
                'scope' => $request->get('scope'),
                'context' => $request->get('context'),
            ]);

            $data = $result->json();
			// dd($data);

            $request->session()->put('store_hash', $data['context']);
            $request->session()->put('access_token', $data['access_token']);
            $request->session()->put('user_id', $data['user']['id']);
            $request->session()->put('user_email', $data['user']['email']);
        } catch (RequestException $e) {
            throw new OauthException("Failed to connect to Oauth Server", 1, $e);
        }
    }

    private function verifySignedRequest($signedRequest)
    {
        list($encodedData, $encodedSignature) = explode('.', $signedRequest, 2);

        // decode the data
        $signature = base64_decode($encodedSignature);
        $jsonStr = base64_decode($encodedData);
        $data = json_decode($jsonStr, true);

        // confirm the signature
        $expectedSignature = hash_hmac('sha256', $jsonStr, $this->appSecret, false);
        if (!hash_equals($expectedSignature, $signature)) {
            Log::error("Recieved invalid Sign Data from BigCommerce");
            return null;
        }
        return $data;
    }

    public function handleLoad(Request $request)
    {
    
    	// dd($request->getCookies())
    
        // $request->session()->regenerate();
        $signedPayload = $request->input('signed_payload');
        if (!empty($signedPayload)) {
       		
            
           $verifiedSignedRequestData = $this->verifySignedRequest($signedPayload, $request);
            if ($verifiedSignedRequestData !== null) {
                $request->session()->put('user_id', $verifiedSignedRequestData['user']['id']);
                $request->session()->put('user_email', $verifiedSignedRequestData['user']['email']);
                $request->session()->put('owner_id', $verifiedSignedRequestData['owner']['id']);
                $request->session()->put('owner_email', $verifiedSignedRequestData['owner']['email']);
                $request->session()->put('store_hash', $verifiedSignedRequestData['context']);
                
            } else {
                throw new OauthException("BigCommerce signature request could not be validated");
            }
        } else {
            throw new OauthException("BigCommerce Sent no signature Data");
        }
    
    	// dd([
    	// 'store_hash' => $request->session()->get('store_hash'),
    	// 'session_hash' => \Illuminate\Support\Facades\Session::getId()
    	// ]);
        	



    }

    public function proxyRequest(Request $request, $endpoint){
    	// dd([
    	// 'store_hash' => $request->session()->get('store_hash'),
    	// 'session_hash' => \Illuminate\Support\Facades\Session::getId()
    	// ]);
    
//     dd(
    
//     [
//     	'auth_token' => $this->getAccessToken($request),
//     	'store' => $this->getStoreHash($request) 
//     ]);
    	
        $requestConfig = [
            'headers' => [
                'X-Auth-Client' => $this->clientId,
                'X-Auth-Token'  => $this->getAccessToken($request),
                'Content-Type'  => 'application/json',
            ]
        ];

        if (
            $request->method() === 'PUT' || 
            $request->method() === 'POST' || 
            $request->method() === 'DELETE'
        ) {
            $requestConfig['body'] = $request->getContent();
        }
    	else{
        	$requestConfig['query'] = $request->all();
        }

        $client = new Client();
        $result = $client->request($request->method(), 'https://api.bigcommerce.com/' . $this->getStoreHash($request) .'/'. $endpoint, $requestConfig);
        
        return response($result->getBody(), $result->getStatusCode())->header('Content-Type', 'application/json');
    }
}