<?php
namespace App\Http\Services;

use App\Contracts\Services\OAuthContract;
use App\Enum\UserPaths;
use App\Models\User;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClientBuilder;
use Google\Auth\CredentialsLoader;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PulkitJalan\Google\Facades\Google;


class OAuthService implements OAuthContract
{
    public $googleClient;
    public $service;

    /**
     *
     */
    public function __construct(){
        $this->googleClient = Google::getClient();
        $this->googleClient->setScopes([
            'https://www.googleapis.com/auth/adwords',
        ]);
        $this->googleClient->setAccessType("offline");
        $this->googleClient->setApprovalPrompt('force');
        $this->googleClient->setPrompt('consent');
    }

    /**
     * @return string
     */
    public function getClientUrl(User|Auth $user) : string{
        return $user->oauth_token != null ? "" : $this->googleClient->createAuthUrl();
    }

    /**
     * @param string $code
     * @return void
     */
    public function saveToken(User|Auth $user , string $code): void{
        $accessToken = $this->googleClient->fetchAccessTokenWithAuthCode($code);
        $refreshToken = isset($accessToken['refresh_token']) ? $accessToken['refresh_token'] : null;
        /** @var JsonResponse $accessToken */
        $accessToken['type'] = 'authorized_user';
        $accessToken['refresh_token'] = $refreshToken;
        $accessToken['client_id'] = $user->id; // Add the client ID here
        $user->oauth_token = $accessToken;
        $user->refresh_token = $refreshToken;
        $user->update();
    }

    /**
     * @return void
     */
    public function setToken(User|Auth $user): void{
        $token = json_decode($user->oauth_token, true);
        $this->googleClient->setAccessToken($token);
    }



    /**
     * @return void
     */
    public function refreshToken(User|Auth $user): void{
        /** @var User $user */
        $user = Auth::user();
        $this->setToken($user);
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
            $accessTokenUpdated['type'] = 'authorized_user';
            $accessTokenUpdated['client_id'] = $user->id; // Add the client ID here

            $user->oauth_token = $accessTokenUpdated;
            $user->update();
            Storage::disk('local')->put(UserPaths::JSON->value , json_encode($accessTokenUpdated));
        }
    }

    public function getCustomerId(Auth|User $user){

        $this->refreshToken($user);
        $token = json_decode($user->oauth_token, true);
        $token['client_secret'] = env('GOOGLE_CLIENT_SECRET');
        $credentials = CredentialsLoader::makeCredentials([], $token);
        // Create a Google Ads API client with the loaded credentials
        $googleAdsClient = (new GoogleAdsClientBuilder())
            ->withDeveloperToken(env('GOOGLE_DEVELOPER_KEY'))
            ->withOAuth2Credential($credentials)->build();
        // Get the customer service client and fetch the first accessible customer's ID
        $customerServiceClient = $googleAdsClient->getCustomerServiceClient();
        $customerId = $customerServiceClient->listAccessibleCustomers()->getResourceNames()[0];
        $user->customer_id = $customerId;
        $user->update();
    }
}
?>
