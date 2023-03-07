<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignMetricRequest;
use App\Http\Requests\UpdateCampaignMetricRequest;
use App\Models\CampaignMetric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class CampaignMetricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignMetricRequest $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignMetric $campaignMetric): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampaignMetric $campaignMetric): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignMetricRequest $request, CampaignMetric $campaignMetric): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignMetric $campaignMetric): RedirectResponse
    {
        //
    }
}
