<?php

namespace App\Http\Controllers\OAuth;

use App\Contracts\Services\OAuthContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OAuthController extends Controller
{
    public function __construct(private readonly OAuthContract $objOAuthContract){

    }

    public function generateToken(){
        return redirect($this->objOAuthContract->getClientUrl());
    }
    public function getToken(Request $request){
        $this->objOAuthContract->saveToken($request->code);
        return redirect(route('dashboard'));
    }
}
