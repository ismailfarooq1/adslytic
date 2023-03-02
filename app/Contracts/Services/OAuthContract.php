<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

interface OAuthContract
{


    public function getClientUrl(Auth|User $user) : string;

    public function saveToken(Auth|User $user , string $code): void;

    public function setToken(Auth|User $user): void;

    public function refreshToken(): void;

    public function getCustomerId(Auth|User $user);
}
