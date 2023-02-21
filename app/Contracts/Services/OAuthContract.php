<?php

namespace App\Contracts\Services;

interface OAuthContract
{


    public function getClientUrl() : string;

    public function saveToken(string $code): void;

    public function setToken(): void;

    public function refreshToken(): void;

}
