<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_ads', function (Blueprint $table) {
            $table->id();
            $table->string("ad_id");
            $table->string("ad_group_id");
            $table->string("status");
            $table->string("clicks");
            $table->string("cost_micros");
            $table->string("impressions");
            $table->string("conversions");
            $table->foreignId("user_id");
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_ads');
    }
};
