<?php

namespace App\Jobs;

use App\Models\User;
use Google\Ads\GoogleAds\Lib\V13\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V13\Services\GoogleAdsRow;
use Google\ApiCore\ApiException;
use Google\Auth\CredentialsLoader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetKeywordView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @throws ApiException
     */
    public function handle(): void
    {
        $customerId = $this->user->customer_id;
        // Get Google Ads client
        $query = sprintf("SELECT ad_group_criterion.id, metrics.clicks, metrics.cost_micros, metrics.impressions, metrics.conversions, ad_group_criterion.position_estimates.first_page_cpc_micros, ad_group_criterion.position_estimates.first_position_cpc_micros, ad_group_criterion.status FROM keyword_view WHERE ad_group_criterion.status != 'REMOVED'");
        $googleAdsResponse = $this->googleAdsClient->getGoogleAdsServiceClient()->search($customerId , $query);

        // Store the retrieved metrics and other required data in the database
        $metrics = [];
        foreach ($googleAdsResponse->getIterator() as $googleAdsRow) {
            /** @var GoogleAdsRow $googleAdsRow */
            $adGroupCriterion = $googleAdsRow->getAdGroupCriterion();
            $adGroupCriterionStatus = $adGroupCriterion->getStatus();
            $positionEstimates = $adGroupCriterion->getPositionEstimates();

            $metrics[] = [
                'customer_id' => $customerId,
                'ad_group_id' => $adGroupCriterion->getAdGroup(),
                'criterion_id' => $adGroupCriterion->getCriterionId(),
                'metrics' => [
                    'clicks' => $googleAdsRow->getMetrics()->getClicks()->getValue(),
                    'cost_micros' => $googleAdsRow->getMetrics()->getCostMicros()->getValue(),
                    'impressions' => $googleAdsRow->getMetrics()->getImpressions()->getValue(),
                    'conversions' => $googleAdsRow->getMetrics()->getConversions()->getValue(),
                ],
                'position_estimates_first_page_cpc_micros' => $positionEstimates->getFirstPageCpcMicros() ? $positionEstimates->getFirstPageCpcMicros()->getValue() : null,
                'position_estimates_first_position_cpc_micros' => $positionEstimates->getFirstPositionCpcMicros() ? $positionEstimates->getFirstPositionCpcMicros()->getValue() : null,
                'status' => $adGroupCriterionStatus ? $adGroupCriterionStatus : null,
            ];
        }

        $this->user->update($metrics);

    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return 'fetch-keyword-view-metrics-' . $this->user->id;
    }
}
