<?php

namespace App\Http\Controllers;

use App\Exceptions\OauthException;
use App\Service\BigCommerceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    private $BigCommerceService;
    public function __construct()
    {
        $this->BigCommerceService = new BigCommerceService();
    }
    public function install(Request $request){
        // dd("Hi");
        
        $this->BigCommerceService->handleInstall($request);
		
        if ($request->has('external_install')) {
            return redirect('https://login.bigcommerce.com/app/' . $this->BigCommerceService->clientId . '/install/succeeded');
        } 

        return redirect(route('home'));
        
    }

    public function load(Request $request){
    
    	
        $this->BigCommerceService->handleLoad($request);

        return redirect(route('home'));
    }

    public function proxyToBigCommerce(Request $request, $endpoint){
        
        return $this->BigCommerceService->proxyRequest($request, $endpoint);
    }
}
