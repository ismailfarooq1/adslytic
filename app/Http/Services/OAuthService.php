<?php
namespace App\Http\Services;

use App\Contracts\Services\OAuthContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PulkitJalan\Google\Facades\Google;

class OAuthService implements OAuthContract
{
    public $googleClient;
    public $service;

    public function __construct(){
        $this->googleClient = Google::getClient();
        $this->googleClient->setScopes([
            'https://www.googleapis.com/auth/adwords',
        ]);
    }

    public function getClientUrl() : string{
        if (Auth::user()->oauth_token != null) {
            return '';
        }

        return $this->googleClient->createAuthUrl();
    }

    public function saveToken(string $code): void{
        $accessToken = $this->googleClient->fetchAccessTokenWithAuthCode($code);
        Auth::user()->oauth_token = $accessToken['access_token'];
        Auth::user()->update();
    }

    public function setToken(): void{
        $token = json_decode(Storage::get(UserPaths::JSON->value));
        $this->googleClient->setAccessToken((array) $token);
    }

    public function refreshToken(): void{
        $this->setToken();
        if ($this->googleClient->isAccessTokenExpired()) {
            // save refresh token to some variable
            $refreshTokenSaved = $this->googleClient->getRefreshToken();

            // update access token
            $this->googleClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);

            // pass access token to some variable
            $accessTokenUpdated = $this->googleClient->getAccessToken();

            // append refresh token
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;

            //Set the new access token
            $accessToken = $refreshTokenSaved;
            $this->googleClient->setAccessToken($accessToken);

            // save to file
            Storage::disk('local')->put(UserPaths::JSON->value , json_encode($accessTokenUpdated));
        }
    }
}
?>
