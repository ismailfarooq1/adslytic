<?php

namespace App\Http\Controllers\OAuth;

use App\Contracts\Services\OAuthContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OAuthController extends Controller
{
    public function __construct(private readonly OAuthContract $objOAuthContract){

    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function generateToken(){
        /** @var User $user */
        $user = Auth::user();
        return redirect($this->objOAuthContract->getClientUrl($user));
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function getToken(Request $request){
        /** @var User $user */
        $user = Auth::user();
        $this->objOAuthContract->saveToken($user , $request->code);

        return redirect(route('dashboard'));
    }
    public function getCustomerId(){
        /** @var User $user */
        $user = Auth::user();
        $this->objOAuthContract->getCustomerId($user);
    }
}
