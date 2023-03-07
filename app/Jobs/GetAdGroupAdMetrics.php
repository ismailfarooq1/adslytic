<?php

namespace App\Jobs;

use App\Models\User;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V13\Services\GoogleAdsRow;
use Google\ApiCore\ApiException;
use Google\Auth\CredentialsLoader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetAdGroupAdMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $user, $googleAdsClient;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $token = json_decode($user->oauth_token, true);
        $token['client_secret'] = env('GOOGLE_CLIENT_SECRET');
        $credentials = CredentialsLoader::makeCredentials([], $token);
        // Create a Google Ads API client with the loaded credentials
        $this->googleAdsClient = (new GoogleAdsClientBuilder())
            ->withDeveloperToken(env("GOOGLE_DEVELOPER_KEY"))
            ->withLoginCustomerId($user->customer_id)
            ->withOAuth2Credential($credentials)->build();
    }

    /**
     * Execute the job.
     * @throws ApiException
     */
    public function handle(): void
    {
        $customerId = $this->user->customer_id;
        // Get Google Ads client
        $query = sprintf("SELECT ad.id, ad.ad_group, ad.status, ad.metrics.clicks, ad.metrics.cost_micros, ad.metrics.impressions, ad.metrics.conversions FROM ad_group_ad ad");
        $googleAdsResponse = $this->googleAdsClient->getGoogleAdsServiceClient()->search($customerId, $query);

        // Save the metrics to the database
        $metrics = [];
        foreach ($googleAdsResponse->getIterator() as $googleAdsRow) {
            /** @var GoogleAdsRow $googleAdsRow */
            $adGroupAd = $googleAdsRow->getAdGroupAd();
            $metrics[] = [
                'ad_id' => $adGroupAd->getAd()->getId(),
                'ad_group_id' => $adGroupAd->getAdGroup(),
                'status' => $adGroupAd->getStatus(),
                'clicks' => $googleAdsRow->getMetrics()->getClicks()->getValue(),
                'cost_micros' => $googleAdsRow->getMetrics()->getCostMicros()->getValue(),
                'impressions' => $googleAdsRow->getMetrics()->getImpressions()->getValue(),
                'conversions' => $googleAdsRow->getMetrics()->getConversions()->getValue(),
            ];
        }

        $this->user->adGroupAds()->upsert($metrics);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
    return 'fetch-ad-group-ad-metrics-' . $this->user->id;
    }
}
