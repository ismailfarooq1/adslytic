<?php

namespace App\Jobs;

use App\Models\CampaignMetric;
use App\Models\User;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsException;
use Google\Auth\CredentialsLoader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetCampaignMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    /**
     * Create a new job instance.
     */
    private $user , $googleAdsClient;

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
     */
    public function handle(): void
    {
        $accessToken = $this->user->oauth_token;
        $customerId = $this->user->customer_id;

        try {

            $query = "SELECT campaign.id,
                             campaign.status,
                             metrics.clicks,
                             metrics.cost_micros,
                             metrics.impressions,
                             metrics.conversions,
                             metrics.all_conversions
                      FROM campaign
                      ORDER BY campaign.id";

            $response = $this->googleAdsClient->getGoogleAdsServiceClient()->search($customerId, $query);
            $campaigns = [];
            foreach ($response->getIterator() as $row) {
                $campaigns[] = [
                    'campaign_id' => $row->getCampaign()->getId(),
                    'status' => $row->getCampaign()->getStatus(),
                    'clicks' => $row->getMetrics()->getClicks()->getValue(),
                    'cost_micros' => $row->getMetrics()->getCostMicros()->getValue(),
                    'impressions' => $row->getMetrics()->getImpressions()->getValue(),
                    'conversions' => $row->getMetrics()->getConversions()->getValue(),
                    'all_conversions' => $row->getMetrics()->getAllConversions()->getValue(),
                ];
            }
            $this->user->campaigns()->upsert($campaigns);
        } catch (GoogleAdsException $ex) {
            $failure = [
                'user_id' => $this->user->id,
                'message' => $ex->getMessage(),
                'request' => $ex->getRequest(),
                'response' => $ex->getResponse(),
            ];
            CampaignMetric::create($failure);
        }
    }
    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return 'fetch-campaign-metrics-' . $this->user->id;
    }
}
