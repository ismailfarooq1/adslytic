<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupAdRequest;
use App\Http\Requests\UpdateGroupAdRequest;
use App\Models\GroupAd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class GroupAdController extends Controller
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
    public function store(StoreGroupAdRequest $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GroupAd $groupAd): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GroupAd $groupAd): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupAdRequest $request, GroupAd $groupAd): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupAd $groupAd): RedirectResponse
    {
        //
    }
}
